<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class OwnerMitraDashboardTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('profiles', function (Blueprint $table): void {
            $table->string('id')->primary();
            $table->string('username')->nullable();
            $table->string('full_name')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('avatar_url')->nullable();
            $table->string('role')->default('pemilik_kucing');
            $table->timestamps();
        });

        Schema::create('cats', function (Blueprint $table): void {
            $table->string('id')->primary();
            $table->string('owner_id');
            $table->string('name');
            $table->string('breed')->nullable();
            $table->string('gender')->nullable();
            $table->string('photo_url')->nullable();
            $table->timestamps();
        });

        Schema::create('mitras', function (Blueprint $table): void {
            $table->string('id')->primary();
            $table->string('owner_id');
            $table->string('name');
            $table->string('address')->nullable();
            $table->string('status')->default('pending');
            $table->string('mitra_type')->default('PAW_SHOP');
            $table->text('description')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('logo_url')->nullable();
            $table->string('photo_url')->nullable();
            $table->text('rules_text')->nullable();
            $table->timestamps();
        });

        Schema::create('mitra_services', function (Blueprint $table): void {
            $table->string('id')->primary();
            $table->string('mitra_id');
            $table->string('name');
            $table->decimal('price', 12, 2)->default(0);
            $table->integer('duration_minutes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('capacity')->nullable();
            $table->timestamps();
        });

        Schema::create('mitra_operating_hours', function (Blueprint $table): void {
            $table->string('id')->primary();
            $table->string('mitra_id');
            $table->integer('day_of_week');
            $table->boolean('is_closed')->default(false);
            $table->boolean('is_24h')->default(false);
            $table->string('open_time')->nullable();
            $table->string('close_time')->nullable();
            $table->timestamps();
        });

        Schema::create('bookings', function (Blueprint $table): void {
            $table->string('id')->primary();
            $table->string('mitra_id');
            $table->string('cat_id')->nullable();
            $table->string('owner_id')->nullable();
            $table->string('service_id')->nullable();
            $table->string('service_name')->nullable();
            $table->decimal('service_price', 12, 2)->default(0);
            $table->integer('queue_number')->nullable();
            $table->string('status')->default('menunggu');
            $table->text('notes')->nullable();
            $table->string('slot_date')->nullable();
            $table->string('slot_start_time')->nullable();
            $table->string('slot_end_time')->nullable();
            $table->timestamp('check_in_at')->nullable();
            $table->timestamp('check_out_at')->nullable();
            $table->timestamps();
        });

        Schema::create('reviews', function (Blueprint $table): void {
            $table->string('id')->primary();
            $table->string('booking_id');
            $table->integer('rating')->default(5);
            $table->text('comment')->nullable();
            $table->timestamps();
        });
    }

    private function ownerSession(string $userId = 'test-owner-uuid'): array
    {
        return [
            'supabase' => [
                'access_token' => 'mock_access_token_jwt',
                'refresh_token' => 'mock_refresh_token',
                'expires_at' => now()->addHour()->timestamp,
                'user' => [
                    'id' => $userId,
                    'email' => 'justinozon53@gmail.com',
                ],
                'profile' => [
                    'full_name' => 'Justinee Owner',
                    'username' => 'justinee',
                    'role' => 'pemilik_kucing',
                ],
            ],
        ];
    }

    public function test_owner_with_mitra_can_access_mitra_dashboard(): void
    {
        $userId = 'test-owner-uuid';

        DB::table('mitras')->insert([
            'id' => 'mitra-uuid-1',
            'owner_id' => $userId,
            'name' => 'Klinik Sehat Ku',
            'status' => 'approved',
            'mitra_type' => 'PAW_HEALTH',
            'address' => 'Jl. Kucing Sehat No. 10',
            'created_at' => now(),
        ]);

        DB::table('mitras')->insert([
            'id' => 'mitra-uuid-2',
            'owner_id' => $userId,
            'name' => 'Giga Grooming',
            'status' => 'approved',
            'mitra_type' => 'PAW_GROOMING',
            'address' => 'Jl. Bulu Cantik No. 5',
            'created_at' => now()->addMinute(),
        ]);

        DB::table('mitra_services')->insert([
            'id' => 'svc-uuid-1',
            'mitra_id' => 'mitra-uuid-1',
            'name' => 'Pemeriksaan Rutin',
            'price' => 150000,
            'duration_minutes' => 30,
            'is_active' => true,
            'created_at' => now(),
        ]);

        $response = $this->withSession($this->ownerSession($userId))
            ->get(route('owner.mitra.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Klinik Sehat Ku');
        $response->assertSee('Beranda');
        $response->assertSee('Booking');
        $response->assertSee('Layanan');
        $response->assertSee('Profil');
        $response->assertSee('Ganti Mitra');
    }

    public function test_owner_without_mitra_sees_empty_state(): void
    {
        $emptyUserId = 'empty-owner-uuid';

        $response = $this->withSession($this->ownerSession($emptyUserId))
            ->get(route('owner.mitra.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Fitur Kemitraan PawKu');
        $response->assertSee('Akun Anda saat ini belum memiliki unit mitra');
    }

    public function test_old_mitra_route_redirects_to_owner_mitra_dashboard(): void
    {
        $response = $this->withSession($this->ownerSession('any-id'))
            ->get('/mitra/dashboard');

        $response->assertRedirect(route('owner.mitra.dashboard'));
    }

    public function test_mitra_root_redirects_to_owner_mitra_dashboard(): void
    {
        $response = $this->withSession($this->ownerSession('any-id'))
            ->get('/mitra');

        $response->assertRedirect(route('owner.mitra.dashboard'));
    }

    public function test_owner_can_view_mitra_list(): void
    {
        $userId = 'test-owner-uuid';

        DB::table('mitras')->insert([
            'id' => 'mitra-uuid-1',
            'owner_id' => $userId,
            'name' => 'Klinik Sehat Ku',
            'status' => 'approved',
            'mitra_type' => 'PAW_HEALTH',
            'address' => 'Jl. Kucing Sehat No. 10',
            'created_at' => now(),
        ]);

        $response = $this->withSession($this->ownerSession($userId))
            ->get(route('owner.mitra'));

        $response->assertStatus(200);
        $response->assertSee('Mitra Saya');
        $response->assertSee('Klinik Sehat Ku');
    }

    public function test_owner_can_view_pending_mitra_status(): void
    {
        $userId = 'test-owner-uuid';
        $pendingId = '00000000-0000-0000-0000-000000000002';

        DB::table('mitras')->insert([
            'id' => $pendingId,
            'owner_id' => $userId,
            'name' => 'Petshop Baru Menunggu',
            'status' => 'pending',
            'mitra_type' => 'PAW_SHOP',
            'created_at' => now(),
        ]);

        $response = $this->withSession($this->ownerSession($userId))
            ->get(route('owner.mitra.status', ['mitra' => $pendingId]));

        $response->assertStatus(200);
        $response->assertSee('Menunggu persetujuan');
        $response->assertSee('Petshop Baru Menunggu');
    }

    public function test_mitra_nested_routes_redirect_to_dashboard_with_tab(): void
    {
        $mitraId = '5c706d40-c30c-4c8c-a5cc-5244af718053';

        $response = $this->withSession($this->ownerSession('test-owner-uuid'))
            ->get("/mitra/{$mitraId}/bookings");

        $response->assertRedirect(route('owner.mitra.dashboard.unit', ['mitra' => $mitraId, 'tab' => 'booking']));

        $response = $this->withSession($this->ownerSession('test-owner-uuid'))
            ->get("/mitra/{$mitraId}/services");

        $response->assertRedirect(route('owner.mitra.dashboard.unit', ['mitra' => $mitraId, 'tab' => 'layanan']));

        $response = $this->withSession($this->ownerSession('test-owner-uuid'))
            ->get("/mitra/{$mitraId}/profile");

        $response->assertRedirect(route('owner.mitra.dashboard.unit', ['mitra' => $mitraId, 'tab' => 'profil']));
    }

    public function test_owner_can_update_booking_status(): void
    {
        $userId = 'test-owner-uuid';
        $mitraId = '5c706d40-c30c-4c8c-a5cc-5244af718053';
        $bookingId = 'booking-uuid-000000000000000000000001';

        DB::table('mitras')->insert([
            'id' => $mitraId,
            'owner_id' => $userId,
            'name' => 'Klinik Sehat Ku',
            'status' => 'approved',
            'created_at' => now(),
        ]);

        DB::table('bookings')->insert([
            'id' => $bookingId,
            'mitra_id' => $mitraId,
            'status' => 'menunggu',
            'created_at' => now(),
        ]);

        $response = $this->withSession($this->ownerSession($userId))
            ->post(route('owner.mitra.booking.status', ['mitra' => $mitraId, 'booking' => $bookingId]), [
                'status' => 'dikonfirmasi',
            ]);

        $response->assertRedirect(route('owner.mitra.dashboard.unit', ['mitra' => $mitraId, 'tab' => 'booking']));
        $this->assertEquals('dikonfirmasi', DB::table('bookings')->where('id', $bookingId)->value('status'));
    }
}
