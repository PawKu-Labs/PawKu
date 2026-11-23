{{--
    Tema oranye admin mitra (PawKu).
    Di-include dari dashboard & booking. Semua warna ada di sini, jadi kalau mau
    ganti warna cukup ubah variabel di :root.
    Selector diberi awalan "body" supaya menang atas CSS bawaan layout.
--}}
<style>
    :root {
        --pk-orange: #F4A03F;       /* oranye tombol Sign In */
        --pk-orange-deep: #E8841A;  /* hover / konfirmasi */
        --pk-brown: #4B2E1E;        /* teks di atas oranye */
        --pk-soft: #FFEFD9;         /* latar lembut oranye */
        --pk-line: #F3E1CA;         /* garis & border */
        --pk-ink: #3A2417;          /* teks utama */
        --pk-muted: #8C7260;        /* teks sekunder */
    }

    /* ---------- Tombol ---------- */
    body .btn { border-radius: 999px; font-weight: 700; letter-spacing: .1px; transition: background-color .15s, box-shadow .15s, border-color .15s, transform .1s; }
    body .btn:active:not(:disabled) { transform: translateY(1px); }
    body .btn:focus-visible { outline: 3px solid rgba(244, 160, 63, .45); outline-offset: 2px; }
    body .btn:disabled, body .btn[disabled] { opacity: .45; cursor: not-allowed; box-shadow: none; }

    body .btn-primary,
    body .btn-success {
        background: var(--pk-orange);
        border: 1px solid var(--pk-orange);
        color: var(--pk-brown);
        box-shadow: 0 6px 16px -6px rgba(232, 132, 26, .55);
    }
    body .btn-primary:hover:not(:disabled),
    body .btn-success:hover:not(:disabled) {
        background: var(--pk-orange-deep);
        border-color: var(--pk-orange-deep);
        color: #fff;
    }

    body .btn-outline {
        background: #fff;
        border: 1.5px solid var(--pk-orange);
        color: var(--pk-brown);
    }
    body .btn-outline:hover:not(:disabled) { background: var(--pk-soft); }

    /* "Tolak" sengaja tetap merah lembut: tindakan yang tidak bisa dibatalkan */
    body .btn-outline-danger {
        background: #fff;
        border: 1.5px solid #F2B8B5;
        color: #B42318;
    }
    body .btn-outline-danger:hover:not(:disabled) { background: #FEF0EF; border-color: #E5847E; }

    body .btn-ghost { color: var(--pk-brown); }
    body .btn-ghost:hover { background: var(--pk-soft); }

    body a.link { color: var(--pk-orange-deep); font-weight: 700; text-decoration: none; }
    body a.link:hover { text-decoration: underline; }

    /* ---------- Kartu & judul ---------- */
    body .page-title { color: var(--pk-ink); }
    body .page-sub { color: var(--pk-muted); }
    body .group-title { color: var(--pk-brown); font-weight: 800; }

    body .card {
        border: 1px solid var(--pk-line);
        border-radius: 20px;
        box-shadow: 0 10px 28px -18px rgba(120, 70, 20, .35);
    }
    body .card.stat { position: relative; overflow: hidden; }
    body .card.stat::before {
        content: "";
        position: absolute; left: 0; top: 16px; bottom: 16px; width: 4px;
        border-radius: 0 4px 4px 0;
        background: var(--pk-orange);
    }
    body .stat-value { color: var(--pk-ink); font-weight: 800; }
    body .stat-label, body .stat-sub { color: var(--pk-muted); }

    /* Ikon statistik: semuanya hangat; merah hanya untuk ditolak / dibatalkan */
    body .stat-icon.i-blue,
    body .stat-icon.i-purple,
    body .stat-icon.i-gold,
    body .stat-icon.i-green { background: var(--pk-soft); color: var(--pk-orange-deep); }
    body .stat-icon.i-red { background: #FDE9E8; color: #B42318; }

    body .hint {
        background: var(--pk-soft);
        border: 1px solid var(--pk-line);
        color: var(--pk-brown);
        border-radius: 16px;
    }

    /* ---------- Nomor antri ---------- */
    body .queue {
        background: var(--pk-orange);
        color: var(--pk-brown);
        font-weight: 800;
        border-radius: 12px;
    }

    /* ---------- Status booking ---------- */
    body .st { border-radius: 999px; font-weight: 700; }
    body .st-menunggu     { background: #FFF1C9; color: #8A5A00; }
    body .st-dikonfirmasi { background: #FFE2BE; color: #9A4A07; }
    body .st-dilayani     { background: #FFD1A0; color: #6F3305; }
    body .st-selesai      { background: #DDF3E4; color: #1D6B3B; }
    body .st-ditolak,
    body .st-dibatalkan   { background: #FDE5E3; color: #B42318; }

    /* ---------- Tab filter ---------- */
    body .tabs { gap: 8px; }
    body .tab {
        border-radius: 999px;
        border: 1px solid var(--pk-line);
        background: #fff;
        color: var(--pk-brown);
        font-weight: 700;
    }
    body .tab:hover { background: var(--pk-soft); }
    body .tab.active {
        background: var(--pk-orange);
        border-color: var(--pk-orange);
        color: var(--pk-brown);
        box-shadow: 0 6px 14px -8px rgba(232, 132, 26, .7);
    }
    body .tab .count { background: rgba(75, 46, 30, .1); border-radius: 999px; }
    body .tab.active .count { background: rgba(75, 46, 30, .18); }

    /* ---------- Kartu booking ---------- */
    body .bk {
        border: 1px solid var(--pk-line);
        border-radius: 22px;
        background: #fff;
        box-shadow: 0 12px 30px -20px rgba(120, 70, 20, .4);
    }
    body .bk-panel {
        background: #FFFAF2;
        border: 1px solid var(--pk-line);
        border-radius: 16px;
    }
    body .panel-title { color: var(--pk-brown); font-weight: 800; }
    body .bk-info .lbl { color: var(--pk-muted); }
    body .bk-info .val { color: var(--pk-ink); }

    body .doc-now {
        background: var(--pk-soft);
        color: var(--pk-brown);
        border: 1px solid var(--pk-line);
        border-radius: 999px;
        font-weight: 700;
    }
    body .doc-none {
        background: #FFF4D6;
        color: #8A5A00;
        border-radius: 999px;
        font-weight: 700;
    }
    body .select {
        border: 1.5px solid var(--pk-line);
        border-radius: 12px;
        background: #fff;
        color: var(--pk-ink);
    }
    body .select:focus { outline: none; border-color: var(--pk-orange); box-shadow: 0 0 0 3px rgba(244, 160, 63, .25); }
    body .act-note { color: #8A5A00; }
    body .act-done { color: var(--pk-muted); }

    /* ---------- Langkah progres ---------- */
    body .step { color: var(--pk-muted); font-weight: 700; }
    body .step i {
        background: #F3EADF; color: var(--pk-muted);
        border-radius: 50%; font-style: normal;
    }
    body .step.now { color: var(--pk-brown); }
    body .step.now i { background: var(--pk-orange); color: var(--pk-brown); box-shadow: 0 0 0 4px rgba(244, 160, 63, .25); }
    body .step.done { color: var(--pk-brown); }
    body .step.done i { background: var(--pk-orange-deep); color: #fff; }
    body .step-sep { background: var(--pk-line); }

    /* ---------- Tabel jadwal ---------- */
    body .tbl thead th { color: var(--pk-muted); border-bottom: 1px solid var(--pk-line); }
    body .tbl tbody tr { border-bottom: 1px solid #F8EEDF; }
    body .tbl tbody tr:hover { background: #FFFAF2; }

    /* ---------- Layanan terlaris ---------- */
    body .bar-track { background: #F6E7D2; border-radius: 999px; }
    body .bar-fill { background: linear-gradient(90deg, var(--pk-orange), var(--pk-orange-deep)); border-radius: 999px; }

    @media (prefers-reduced-motion: reduce) {
        body .btn { transition: none; }
    }
</style>