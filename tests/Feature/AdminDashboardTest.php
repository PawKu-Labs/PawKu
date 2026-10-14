<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
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
            $table->string('owner_id')->nullable();
            $table->string('name')->nullable();
            $table->timestamps();
        });

        Schema::create('mitras', function (Blueprint $table): void {
            $table->string('id')->primary();
            $table->string('owner_id');
            $table->string('name');
            $table->string('address')->nullable();
            $table->string('status')->default('pending');
            $table->string('mitra_type')->default('PAW_CARE');
            $table->text('description')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('logo_url')->nullable();
            $table->string('photo_url')->nullable();
            $table->text('rules_text')->nullable();
            $table->string('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->text('owner_note')->nullable();
            $table->timestamps();
        });

        Schema::create('bookings', function (Blueprint $table): void {
            $table->string('id')->primary();
            $table->string('mitra_id')->nullable();
            $table->timestamps();
        });
    }

    private function adminSession(): array
    {
        return [
            'supabase' => [
                'access_token' => 'admin_test_token',
                'refresh_token' => 'admin_refresh_token',
                'expires_at' => now()->addHour()->timestamp,
                'user' => [
                    'id' => '00000000-0000-0000-0000-000000000001',
                    'email' => 'admin@pawku.com',
                ],
                'profile' => [
                    'full_name' => 'Admin PawKu',
                    'role' => 'admin',
                ],
            ],
        ];
    }

    public function test_unauthenticated_user_cannot_access_admin_dashboard(): void
    {
        $response = $this->get(route('admin.dashboard'));
        $response->assertRedirect(route('login'));
    }

    public function test_owner_role_cannot_access_admin_dashboard(): void
    {
        $ownerSession = [
            'supabase' => [
                'access_token' => 'owner_token',
                'profile' => ['role' => 'pemilik_kucing'],
            ],
        ];

        $response = $this->withSession($ownerSession)->get(route('admin.dashboard'));
        $response->assertStatus(403);
    }

    public function test_admin_can_view_monitoring_dashboard(): void
    {
        DB::table('profiles')->insert([
            ['id' => 'user-1', 'username' => 'owner1', 'role' => 'pemilik_kucing', 'created_at' => now()],
            ['id' => 'admin-1', 'username' => 'admin1', 'role' => 'admin', 'created_at' => now()],
        ]);

        DB::table('mitras')->insert([
            'id' => 'mitra-1',
            'owner_id' => 'user-1',
            'name' => 'Klinik Meow',
            'status' => 'pending',
            'created_at' => now(),
        ]);

        DB::table('cats')->insert([
            'id' => 'cat-1',
            'name' => 'Mimi',
            'created_at' => now(),
        ]);

        $response = $this->withSession($this->adminSession())->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Dashboard Monitoring');
        $response->assertSee('Total Pemilik Kucing');
        $response->assertSee('Total Mitra Petshop');
        $response->assertSee('Total Admin');
        $response->assertSee('Status Petshop');
        $response->assertSee('Total Disetujui');
        $response->assertSee('Menunggu Persetujuan');
        $response->assertSee('Total Ditolak');
        $response->assertSee('Total Booking');
        $response->assertSee('Total Profil Kucing');
    }

    public function test_admin_can_view_petshop_approvals(): void
    {
        DB::table('profiles')->insert([
            'id' => 'user-1',
            'full_name' => 'Budi Santoso',
            'role' => 'pemilik_kucing',
            'created_at' => now(),
        ]);

        DB::table('mitras')->insert([
            'id' => 'mitra-1',
            'owner_id' => 'user-1',
            'name' => 'Happy Cat Hotel',
            'address' => 'Jl. Kucing Bahagia No. 10',
            'status' => 'pending',
            'created_at' => now(),
        ]);

        $response = $this->withSession($this->adminSession())
            ->get(route('admin.petshops', ['status' => 'pending']));

        $response->assertStatus(200);
        $response->assertSee('Persetujuan Petshop');
        $response->assertSee('Happy Cat Hotel');
        $response->assertSee('Budi Santoso');
        $response->assertSee('Setujui');
        $response->assertSee('Tolak');
    }

    public function test_admin_can_approve_petshop(): void
    {
        DB::table('mitras')->insert([
            'id' => 'mitra-test-1',
            'owner_id' => '00000000-0000-0000-0000-000000000001',
            'name' => 'Cat Grooming Center',
            'status' => 'pending',
            'created_at' => now(),
        ]);

        $response = $this->withSession($this->adminSession())
            ->post(route('admin.petshops.status', 'mitra-test-1'), [
                'status' => 'approved',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('mitras', [
            'id' => 'mitra-test-1',
            'status' => 'approved',
        ]);
    }

    public function test_admin_can_reject_petshop_with_reason(): void
    {
        DB::table('mitras')->insert([
            'id' => 'mitra-test-2',
            'owner_id' => '00000000-0000-0000-0000-000000000001',
            'name' => 'Cat Clinic Invalid',
            'status' => 'pending',
            'created_at' => now(),
        ]);

        $response = $this->withSession($this->adminSession())
            ->post(route('admin.petshops.status', 'mitra-test-2'), [
                'status' => 'rejected',
                'rejection_reason' => 'Dokumen izin klinik belum lengkap.',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('mitras', [
            'id' => 'mitra-test-2',
            'status' => 'rejected',
            'rejection_reason' => 'Dokumen izin klinik belum lengkap.',
        ]);
    }
}
