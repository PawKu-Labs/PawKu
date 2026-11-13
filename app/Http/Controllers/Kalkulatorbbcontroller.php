<?php

// Simpan di: app/Http/Controllers/KalkulatorBbController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class KalkulatorBbController extends Controller
{
    /**
     * Basis pengetahuan: rentang berat ideal kucing dewasa per ras (kg).
     * Nilai ini perkiraan umum. Jika sudah ada tabel breed_weight_standards,
     * ganti konstanta ini dengan data dari database.
     */
    private const STANDAR_RAS = [
        'Domestik / Kampung' => ['min' => 3.0, 'max' => 5.0],
        'Persia'             => ['min' => 3.5, 'max' => 5.5],
        'Anggora'            => ['min' => 3.0, 'max' => 5.0],
        'Russian Blue'       => ['min' => 3.0, 'max' => 5.5],
        'Bengal'             => ['min' => 3.5, 'max' => 6.5],
        'Maine Coon'         => ['min' => 5.0, 'max' => 8.0],
        'British Shorthair'  => ['min' => 4.0, 'max' => 7.0],
        'Siamese'            => ['min' => 3.0, 'max' => 5.0],
        'Ragdoll'            => ['min' => 4.5, 'max' => 7.5],
        'Scottish Fold'      => ['min' => 3.0, 'max' => 5.5],
        'Sphynx'             => ['min' => 3.5, 'max' => 5.5],
        'Arabian'            => ['min' => 3.0, 'max' => 5.0],
    ];

    private const BATAS_DEWASA_BULAN = 12;

    private const SARAN = [
        'kurus' => [
            'Tambah porsi makan secara bertahap dan pilih pakan berkalori serta berprotein cukup.',
            'Berikan makan lebih sering dalam porsi kecil.',
            'Periksa kemungkinan cacingan atau masalah kesehatan lain ke dokter hewan.',
            'Timbang rutin sebulan sekali untuk memantau kenaikan berat.',
        ],
        'normal' => [
            'Pertahankan porsi makan dan jenis pakan yang sekarang.',
            'Tetap ajak bermain setiap hari supaya kucing aktif.',
            'Timbang rutin sebulan sekali untuk menjaga berat tetap stabil.',
            'Tetap lakukan pemeriksaan dan vaksin sesuai jadwal ke dokter hewan.',
        ],
        'gemuk' => [
            'Kurangi porsi makan secara bertahap dan hindari camilan berlebih.',
            'Tambah waktu bermain dan aktivitas (berburu mainan, memanjat).',
            'Pilih pakan kontrol berat badan sesuai anjuran dokter hewan.',
            'Konsultasikan program diet ke dokter hewan, jangan menurunkan berat secara drastis.',
        ],
        'unknown' => [
            'Kucing masih dalam masa pertumbuhan, berat ideal belum dapat dipastikan.',
            'Catat berat badan secara berkala dan pantau tren kenaikannya.',
            'Berikan pakan khusus anak kucing (kitten) dan vaksin sesuai jadwal.',
            'Konsultasikan ke dokter hewan bila pertumbuhan terlihat terlalu lambat atau cepat.',
        ],
    ];

    public function index()
    {
        return view('kalkulator_bb', [
            'daftarRas' => array_keys(self::STANDAR_RAS),
            'hasil'     => null,
        ]);
    }

    public function hitung(Request $request)
    {
        $data = $request->validate([
            'ras'    => ['required', Rule::in(array_keys(self::STANDAR_RAS))],
            'umur'   => ['required', 'numeric', 'min:0', 'max:300'],
            'satuan' => ['required', Rule::in(['bulan', 'tahun'])],
            'bb'     => ['required', 'numeric', 'min:0.1', 'max:20'],
        ], [
            'ras.required'  => 'Pilih ras kucing.',
            'umur.required' => 'Umur wajib diisi.',
            'umur.numeric'  => 'Umur harus berupa angka.',
            'bb.required'   => 'Berat badan wajib diisi.',
            'bb.numeric'    => 'Berat badan harus berupa angka.',
            'bb.min'        => 'Berat badan harus lebih dari 0 kg.',
        ]);

        $umurBulan = $data['satuan'] === 'tahun' ? $data['umur'] * 12 : $data['umur'];

        return view('kalkulator_bb', [
            'daftarRas' => array_keys(self::STANDAR_RAS),
            'hasil'     => $this->klasifikasi($umurBulan, (float) $data['bb'], $data['ras']),
        ]);
    }

    /**
     * Decision tree / rule-based classifier.
     */
    private function klasifikasi(float $umurBulan, float $bb, string $ras): array
    {
        $std   = self::STANDAR_RAS[$ras];
        $min   = $std['min'];
        $max   = $std['max'];
        $trace = ["umur = {$umurBulan} bulan, berat = {$bb} kg, ras = {$ras}"];

        // Cabang 1: anak kucing
        if ($umurBulan < self::BATAS_DEWASA_BULAN) {
            $trace[] = 'umur < ' . self::BATAS_DEWASA_BULAN . ' bulan -> anak kucing -> BELUM DIKETAHUI';
            $status = 'unknown';
        } else {
            $trace[] = 'umur >= ' . self::BATAS_DEWASA_BULAN . " bulan -> dewasa, rentang ideal {$min}-{$max} kg";

            // Cabang 2-4: bandingkan dengan rentang ideal
            if ($bb < $min) {
                $trace[] = "berat {$bb} < {$min} -> KURUS";
                $status = 'kurus';
            } elseif ($bb <= $max) {
                $trace[] = "{$min} <= berat {$bb} <= {$max} -> NORMAL (IDEAL)";
                $status = 'normal';
            } else {
                $trace[] = "berat {$bb} > {$max} -> GEMUK";
                $status = 'gemuk';
            }
        }

        return [
            'ras'     => $ras,
            'bb'      => $bb,
            'status'  => $status,
            'label'   => ['kurus' => 'Kurus', 'normal' => 'Normal', 'gemuk' => 'Gemuk', 'unknown' => 'Belum diketahui'][$status],
            'min'     => $min,
            'max'     => $max,
            'posisi'  => $status === 'unknown' ? null : $this->posisiMarker($bb, $min, $max),
            'saran'   => self::SARAN[$status],
            'trace'   => $trace,
        ];
    }

    /**
     * Posisi marker pada bar (0-100%): 30% kurus | 40% ideal | 30% gemuk.
     */
    private function posisiMarker(float $bb, float $min, float $max): float
    {
        $lebar = $max - $min;
        $bawah = $min - $lebar * 0.75;
        $atas  = $max + $lebar * 0.75;

        if ($bb < $min) {
            return 30 * max(0, ($bb - $bawah) / ($min - $bawah));
        }
        if ($bb <= $max) {
            return 30 + 40 * (($bb - $min) / ($max - $min));
        }
        return 70 + 30 * min(1, ($bb - $max) / ($atas - $max));
    }
}