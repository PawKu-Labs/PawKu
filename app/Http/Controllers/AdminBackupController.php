<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use ZipArchive;

class AdminBackupController extends Controller
{
    // key = nama tabel di Supabase, value = label tampilan
    private const TABLES = [
        'bookings' => 'Booking Layanan',
        'cats'     => 'Data Kucing',
        'petshops' => 'Petshops & Mitra',
        'profiles' => 'Daftar Pengguna',
    ];

    public function index()
    {
        return view('admin.backup-data', [
            'tables'  => self::TABLES,
            'user'    => session('user', []),
            'profile' => session('profile', []),
            'stats'   => session('stats', []),
        ]);
    }

    public function download(Request $request)
    {
        $data = $request->validate([
            'tables'   => ['required', 'array', 'min:1'],
            'tables.*' => ['in:' . implode(',', array_keys(self::TABLES))],
            'format'   => ['required', 'in:json,csv'],
        ]);

        $stamp = now()->setTimezone('Asia/Jakarta')->format('Ymd_His');
        $result = [];

        try {
            foreach ($data['tables'] as $table) {
                $result[$table] = $this->fetchAll($table);
            }
        } catch (\Throwable $e) {
            Log::error('Backup gagal: ' . $e->getMessage());
            return back()->with('error', 'Backup gagal. Periksa log server.');
        }

        if ($data['format'] === 'json') {
            $json = json_encode(
                ['exported_at' => now()->toIso8601String(), 'data' => $result],
                JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
            );

            return response($json, 200, [
                'Content-Type'        => 'application/json',
                'Content-Disposition' => "attachment; filename=\"pawku_backup_{$stamp}.json\"",
            ]);
        }

        // CSV -> ZIP (satu file CSV per tabel)
        $zipPath = tempnam(sys_get_temp_dir(), 'pawku');
        $zip = new ZipArchive();
        $zip->open($zipPath, ZipArchive::OVERWRITE);

        foreach ($result as $table => $rows) {
            $zip->addFromString("{$table}.csv", $this->toCsv($rows));
        }
        $zip->close();

        return response()->download($zipPath, "pawku_backup_{$stamp}.zip")->deleteFileAfterSend(true);
    }

    /** Ambil semua baris (Supabase membatasi 1000 baris/request, jadi dipaginasi). */
    private function fetchAll(string $table): array
    {
        $url  = rtrim(config('services.supabase.url'), '/') . "/rest/v1/{$table}";
        $key  = config('services.supabase.service_key');
        $size = 1000;
        $from = 0;
        $all  = [];

        do {
            $res = Http::withHeaders([
                'apikey'        => $key,
                'Authorization' => "Bearer {$key}",
                'Range'         => "{$from}-" . ($from + $size - 1),
            ])->get($url, ['select' => '*'])->throw();

            $rows = $res->json() ?? [];
            $all  = array_merge($all, $rows);
            $from += $size;
        } while (count($rows) === $size);

        return $all;
    }

    private function toCsv(array $rows): string
    {
        $out = fopen('php://temp', 'r+');
        fwrite($out, "\xEF\xBB\xBF"); // BOM agar Excel membaca UTF-8 dengan benar

        if ($rows) {
            $headers = array_keys($rows[0]);
            fputcsv($out, $headers);

            foreach ($rows as $row) {
                fputcsv($out, array_map(function ($h) use ($row) {
                    $v = $row[$h] ?? '';
                    $v = is_array($v) ? json_encode($v, JSON_UNESCAPED_UNICODE) : (string) $v;
                    // cegah CSV/formula injection di Excel
                    return preg_match('/^[=+\-@]/', $v) ? "'" . $v : $v;
                }, $headers));
            }
        }

        rewind($out);
        $csv = stream_get_contents($out);
        fclose($out);
        return $csv;
    }
}