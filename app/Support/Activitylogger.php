<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

/**
 * Pencatat aktivitas dari sisi aplikasi (login, logout, aksi admin).
 * Perubahan data & transaksi dicatat otomatis oleh trigger database.
 *
 * Contoh:
 *   ActivityLogger::log('auth', 'login', 'Login ke aplikasi');
 *
 * Gagal mencatat tidak boleh merusak alur utama, jadi semua error hanya di-report.
 * Jangan panggil di dalam DB::transaction() yang sedang berjalan (di PostgreSQL,
 * error insert akan menggagalkan seluruh transaksi).
 */
class ActivityLogger
{
    public static function log(
        string $category,
        string $action,
        string $description,
        ?string $subjectType = null,
        string|int|null $subjectId = null,
        ?array $old = null,
        ?array $new = null,
    ): void {
        try {
            $profile = session('supabase.profile', []);
            $user = session('supabase.user', []);

            DB::table('activity_logs')->insert([
                'actor_id' => $user['id'] ?? null,
                'actor_name' => $profile['full_name'] ?? $profile['username'] ?? ($user['email'] ?? null),
                'actor_role' => $profile['role'] ?? null,
                'category' => $category,
                'action' => $action,
                'subject_type' => $subjectType,
                'subject_id' => $subjectId !== null ? (string) $subjectId : null,
                'description' => $description,
                'old_values' => $old ? json_encode($old) : null,
                'new_values' => $new ? json_encode($new) : null,
                'source' => 'app',
                'ip_address' => request()->ip(),
                'created_at' => now(),
            ]);
        } catch (\Throwable $e) {
            report($e);
        }
    }
}