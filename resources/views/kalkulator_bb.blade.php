{{-- Simpan di: resources/views/kalkulator_bb/index.blade.php --}}
{{-- Jika proyek Anda sudah punya layout, ganti kerangka <html> ini dengan
     @extends('layouts.app') lalu bungkus isi dalam @section('content') ... @endsection --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PawKu - Cek Status Berat Badan Kucing</title>
    <style>
        :root {
            --orange: #F39C45;
            --orange-dark: #E67E22;
            --cream: #FDF1E4;
            --green: #3DAE6E;
            --red: #E26A5F;
            --yellow: #E9B949;
            --text: #4a3426;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0; padding: 24px 16px;
            font-family: "Segoe UI", Roboto, Arial, sans-serif;
            background: var(--cream); color: var(--text);
            display: flex; justify-content: center;
        }
        .card {
            width: 100%; max-width: 460px; background: #fff;
            border-radius: 20px; padding: 24px;
            box-shadow: 0 6px 24px rgba(230,126,34,.15);
        }
        .back { font-size: 13px; color: var(--orange-dark); text-decoration: none; }
        h1 { margin: 10px 0 4px; font-size: 22px; }
        h1 span { color: var(--orange-dark); }
        .sub { margin: 0 0 12px; font-size: 14px; opacity: .75; }
        label { display: block; font-size: 13px; font-weight: 600; margin: 14px 0 6px; }
        input, select {
            width: 100%; padding: 12px 14px; font-size: 15px;
            border: 1.5px solid #f0dcc6; border-radius: 12px;
            background: var(--cream); color: var(--text); outline: none;
        }
        input:focus, select:focus { border-color: var(--orange); }
        .row { display: flex; gap: 10px; }
        .row > div { flex: 1; }
        button {
            width: 100%; margin-top: 20px; padding: 14px;
            font-size: 16px; font-weight: 700; color: #fff;
            background: var(--orange-dark); border: 0; border-radius: 14px; cursor: pointer;
        }
        button:hover { background: var(--orange); }
        .error { color: var(--red); font-size: 12.5px; margin-top: 5px; }
        .hasil { margin-top: 22px; }
        .status-box { border-radius: 16px; padding: 16px; display: flex; justify-content: space-between; align-items: center; }
        .status-box .label { font-size: 12px; opacity: .8; }
        .status-box .value { font-size: 24px; font-weight: 800; }
        .status-box .bb { text-align: right; font-size: 20px; font-weight: 700; }
        .s-kurus   { background: #fdf3d9; color: #9a7410; }
        .s-normal  { background: #e3f6ec; color: #217a4a; }
        .s-gemuk   { background: #fde6e3; color: #b3443a; }
        .s-unknown { background: #eee; color: #555; }
        .bar-wrap { margin-top: 18px; }
        .bar { position: relative; height: 12px; border-radius: 10px;
            background: linear-gradient(to right, var(--yellow) 0 30%, var(--green) 30% 70%, var(--red) 70% 100%); }
        .marker { position: absolute; top: -5px; width: 22px; height: 22px; background: #fff;
            border: 4px solid var(--text); border-radius: 50%; transform: translateX(-50%); }
        .bar-labels { display: flex; justify-content: space-between; font-size: 12px; margin-top: 8px; opacity: .8; }
        .range { text-align: center; font-size: 13px; margin-top: 8px; font-weight: 600; }
        .saran { margin-top: 18px; background: var(--cream); border-radius: 14px; padding: 14px 16px; }
        .saran h3 { margin: 0 0 8px; font-size: 15px; }
        .saran ul { margin: 0; padding-left: 18px; font-size: 14px; line-height: 1.6; }
        .trace { margin-top: 14px; font-size: 12px; background: #f7f7f7; border-radius: 10px; padding: 10px 12px; font-family: Consolas, monospace; line-height: 1.6; }
        .disclaimer { margin-top: 14px; font-size: 12px; opacity: .7; line-height: 1.5; }
    </style>
</head>
<body>
<div class="card">
    <a class="back" href="{{ url()->previous() }}">&larr; Kembali</a>
    <h1>Cek Status Berat Badan <span>PawKu</span></h1>
    <p class="sub">Masukkan ras, umur, dan berat badan kucing.</p>

    <form method="POST" action="{{ route('kalkulator_bb.hitung') }}">
        @csrf

        <label for="ras">Ras kucing</label>
        <select id="ras" name="ras">
            <option value="">-- Pilih ras --</option>
            @foreach ($daftarRas as $r)
                <option value="{{ $r }}" @selected(old('ras') === $r)>{{ $r }}</option>
            @endforeach
        </select>
        @error('ras') <div class="error">{{ $message }}</div> @enderror

        <div class="row">
            <div>
                <label for="umur">Umur</label>
                <input type="number" id="umur" name="umur" min="0" step="1"
                       value="{{ old('umur') }}" placeholder="mis. 24">
                @error('umur') <div class="error">{{ $message }}</div> @enderror
            </div>
            <div>
                <label for="satuan">Satuan</label>
                <select id="satuan" name="satuan">
                    <option value="bulan" @selected(old('satuan', 'bulan') === 'bulan')>Bulan</option>
                    <option value="tahun" @selected(old('satuan') === 'tahun')>Tahun</option>
                </select>
            </div>
        </div>

        <label for="bb">Berat badan (kg)</label>
        <input type="number" id="bb" name="bb" min="0" step="0.1"
               value="{{ old('bb') }}" placeholder="mis. 4.6">
        @error('bb') <div class="error">{{ $message }}</div> @enderror

        <button type="submit">Cek Status</button>
    </form>

    @if ($hasil)
        <div class="hasil">
            <div class="status-box s-{{ $hasil['status'] }}">
                <div>
                    <div class="label">Status berat badan</div>
                    <div class="value">{{ $hasil['label'] }}</div>
                </div>
                <div>
                    <div class="label">Berat saat ini</div>
                    <div class="bb">{{ number_format($hasil['bb'], 1) }} kg</div>
                </div>
            </div>

            @if ($hasil['posisi'] !== null)
                <div class="bar-wrap">
                    <div class="bar">
                        <div class="marker" style="left: {{ round($hasil['posisi'], 1) }}%"></div>
                    </div>
                    <div class="bar-labels"><span>Kurus</span><span>Ideal</span><span>Gemuk</span></div>
                    <div class="range">
                        Rentang ideal {{ number_format($hasil['min'], 1) }} - {{ number_format($hasil['max'], 1) }} kg ({{ $hasil['ras'] }})
                    </div>
                </div>
            @endif

            <div class="saran">
                <h3>Saran perawatan</h3>
                <ul>
                    @foreach ($hasil['saran'] as $s)
                        <li>{{ $s }}</li>
                    @endforeach
                </ul>
            </div>

            <div class="trace">
                <b>Alur keputusan:</b><br>
                @foreach ($hasil['trace'] as $t)
                    &rarr; {{ $t }}<br>
                @endforeach
            </div>

            <p class="disclaimer">
                Hasil ini adalah informasi awal berdasarkan data yang dimasukkan dan bukan
                pengganti diagnosis dokter hewan.
            </p>
        </div>
    @endif
</div>
</body>
</html>