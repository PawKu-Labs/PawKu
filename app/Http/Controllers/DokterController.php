<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * PROTOTIPE FRONT END.
 * Semua data di bawah adalah contoh statis supaya tampilan bisa dilihat.
 * Ganti method dummy*() dengan query ke tabel mitra_members, medical_records,
 * prescriptions, dst. sesuai file pawku_rekam_medis_schema.sql.
 */
class DokterController extends Controller
{
    public function dashboard()
    {
        return view('dokter.dashboard', [
            'stats' => [
                'antrian' => 5,
                'selesai' => 3,
                'draft' => 2,
                'kontrol' => 4,
            ],
            'antrian' => array_slice($this->dummyAntrian(), 0, 4),
            'draftList' => [
                ['cat' => 'Mochi', 'tanggal' => '12 Nov 2026, 09.10', 'keluhan' => 'Muntah dan lesu sejak kemarin'],
                ['cat' => 'Luna', 'tanggal' => '11 Nov 2026, 15.40', 'keluhan' => 'Kontrol pasca steril'],
            ],
            'kontrol' => [
                ['cat' => 'Oreo', 'tanggal' => '13 Nov 2026', 'alasan' => 'Kontrol infeksi saluran kemih'],
                ['cat' => 'Bella', 'tanggal' => '14 Nov 2026', 'alasan' => 'Vaksin F4 dosis kedua'],
                ['cat' => 'Simba', 'tanggal' => '16 Nov 2026', 'alasan' => 'Cek hasil lab ginjal'],
            ],
        ]);
    }

    public function pasien(Request $request)
    {
        return view('dokter.index', [
            'antrian' => $this->dummyAntrian(),
            'pasien' => array_values($this->dummyKucing()),
            'q' => $request->query('q'),
        ]);
    }

    public function pasienShow($id)
    {
        $cat = $this->dummyKucing()[$id] ?? abort(404);

        return view('dokter.show', [
            'cat' => $cat,
            'timeline' => [
                [
                    'tipe' => 'verified',
                    'tanggal' => '28 Okt 2026',
                    'judul' => 'Pemeriksaan: infeksi saluran kemih',
                    'isi' => 'Disuria dan sering ke litter box. Suhu 38,6 °C, BB 4,2 kg. Diberi amoxicillin 3 hari, kontrol 5 hari lagi.',
                    'sumber' => 'drh. Rina Lestari, Klinik PAW_HEALTH Batam Center',
                    'status' => 'final',
                ],
                [
                    'tipe' => 'observasi',
                    'tanggal' => '15 Okt 2026',
                    'judul' => 'Catatan grooming',
                    'isi' => 'Kulit di area punggung tampak kemerahan, tidak ada kutu terlihat.',
                    'sumber' => 'PAW_GROOMING Happy Paws',
                    'status' => null,
                ],
                [
                    'tipe' => 'owner',
                    'tanggal' => '10 Okt 2026',
                    'judul' => 'Gejala di rumah',
                    'isi' => 'Nafsu makan turun dua hari, lebih banyak tidur.',
                    'sumber' => 'Dilaporkan owner',
                    'status' => null,
                ],
                [
                    'tipe' => 'verified',
                    'tanggal' => '02 Agu 2026',
                    'judul' => 'Vaksin F4 dosis pertama',
                    'isi' => 'Batch VX-2291. Dosis kedua dijadwalkan 4 minggu kemudian.',
                    'sumber' => 'drh. Rina Lestari, Klinik PAW_HEALTH Batam Center',
                    'status' => 'final',
                ],
            ],
        ]);
    }

    public function pemeriksaanCreate(Request $request)
    {
        $kucing = $this->dummyKucing();
        $cat = $kucing[$request->query('cat')] ?? reset($kucing);

        return view('dokter.create', [
            'cat' => $cat,
            'daftarKucing' => $kucing,
        ]);
    }

    public function pemeriksaanStore(Request $request)
    {
        // TODO: validasi, simpan ke medical_records (status draft/final),
        // prescriptions, procedures, lampiran. Setelah final: kunci catatan.
        $final = $request->input('action') === 'final';

        return redirect()
            ->route('dokter.rekam-medis')
            ->with('success', $final ? 'Rekam medis difinalisasi.' : 'Draf rekam medis disimpan.');
    }

    public function rekamMedis()
    {
        return view('dokter.rekam-medis', [
            'records' => [
                ['id' => 101, 'tanggal' => '12 Nov 2026', 'cat' => 'Mochi', 'owner' => 'Andi', 'diagnosis' => 'Gastritis ringan', 'status' => 'draft'],
                ['id' => 100, 'tanggal' => '11 Nov 2026', 'cat' => 'Luna', 'owner' => 'Sari', 'diagnosis' => 'Kontrol pasca steril, sembuh baik', 'status' => 'draft'],
                ['id' => 99, 'tanggal' => '10 Nov 2026', 'cat' => 'Oreo', 'owner' => 'Budi', 'diagnosis' => 'Infeksi saluran kemih', 'status' => 'final'],
                ['id' => 98, 'tanggal' => '08 Nov 2026', 'cat' => 'Bella', 'owner' => 'Dewi', 'diagnosis' => 'Vaksinasi F4 dosis pertama', 'status' => 'final'],
                ['id' => 97, 'tanggal' => '02 Nov 2026', 'cat' => 'Simba', 'owner' => 'Rudi', 'diagnosis' => 'Dugaan gangguan ginjal kronis', 'status' => 'koreksi'],
            ],
        ]);
    }

    public function profil()
    {
        return view('dokter.profil', [
            'dokter' => [
                'str' => '3201.12.34567',
                'sip' => 'SIP/DH/2025/0091',
                'spesialisasi' => 'Kedokteran hewan umum, kucing',
                'status' => session('dokter.verification', 'verified'),
            ],
            'klinik' => [
                ['nama' => 'Klinik PAW_HEALTH Batam Center', 'peran' => 'Dokter', 'aktif' => true],
                ['nama' => 'Klinik PAW_HEALTH Nagoya', 'peran' => 'Dokter', 'aktif' => true],
            ],
        ]);
    }

    public function profilUpdate(Request $request)
    {
        // TODO: simpan STR/SIP/spesialisasi, ubah verification_status jadi 'pending'.
        return back()->with('success', 'Data praktik disimpan dan diajukan untuk verifikasi.');
    }

    public function switchKlinik(Request $request)
    {
        session(['dokter.active_clinic' => (int) $request->input('clinic_id')]);

        return back()->with('success', 'Klinik aktif diganti.');
    }

    /* ------------------------- Data contoh ------------------------- */

    private function dummyKucing(): array
    {
        return [
            1 => ['id' => 1, 'nama' => 'Mochi', 'ras' => 'Domestik', 'umur' => '2 tahun', 'jk' => 'Jantan', 'steril' => 'Sudah', 'berat' => '4,2', 'warna' => 'Putih oranye', 'microchip' => '-', 'golongan' => 'A', 'owner' => 'Andi', 'alergi' => ['Penisilin'], 'kondisi' => []],
            2 => ['id' => 2, 'nama' => 'Luna', 'ras' => 'Persia', 'umur' => '1 tahun', 'jk' => 'Betina', 'steril' => 'Sudah', 'berat' => '3,6', 'warna' => 'Abu-abu', 'microchip' => '9850001122', 'golongan' => 'B', 'owner' => 'Sari', 'alergi' => [], 'kondisi' => []],
            3 => ['id' => 3, 'nama' => 'Oreo', 'ras' => 'Domestik', 'umur' => '5 tahun', 'jk' => 'Jantan', 'steril' => 'Sudah', 'berat' => '5,1', 'warna' => 'Hitam putih', 'microchip' => '-', 'golongan' => '-', 'owner' => 'Budi', 'alergi' => [], 'kondisi' => ['Infeksi saluran kemih berulang']],
            4 => ['id' => 4, 'nama' => 'Bella', 'ras' => 'Anggora', 'umur' => '8 bulan', 'jk' => 'Betina', 'steril' => 'Belum', 'berat' => '2,9', 'warna' => 'Putih', 'microchip' => '-', 'golongan' => '-', 'owner' => 'Dewi', 'alergi' => [], 'kondisi' => []],
            5 => ['id' => 5, 'nama' => 'Simba', 'ras' => 'Maine Coon', 'umur' => '9 tahun', 'jk' => 'Jantan', 'steril' => 'Sudah', 'berat' => '6,8', 'warna' => 'Cokelat tabby', 'microchip' => '9850003344', 'golongan' => 'A', 'owner' => 'Rudi', 'alergi' => ['Ikan'], 'kondisi' => ['Gangguan ginjal (dugaan)']],
        ];
    }

    private function dummyAntrian(): array
    {
        $k = $this->dummyKucing();

        return [
            ['jam' => '09.00', 'cat' => $k[1], 'layanan' => 'Pemeriksaan umum', 'status' => 'Sedang diperiksa'],
            ['jam' => '09.45', 'cat' => $k[4], 'layanan' => 'Vaksinasi', 'status' => 'Menunggu'],
            ['jam' => '10.30', 'cat' => $k[3], 'layanan' => 'Kontrol', 'status' => 'Menunggu'],
            ['jam' => '11.15', 'cat' => $k[5], 'layanan' => 'Pemeriksaan umum', 'status' => 'Menunggu'],
            ['jam' => '13.00', 'cat' => $k[2], 'layanan' => 'Kontrol pasca steril', 'status' => 'Menunggu'],
        ];
    }
}