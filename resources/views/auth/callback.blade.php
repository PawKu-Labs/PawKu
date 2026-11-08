<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Memproses login...</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/icon/pawku-nobg.png') }}">
</head>

<body>
    <p>Memproses login...</p>
    <script>
        (async () => {
            const params = new URLSearchParams(window.location.hash.substring(1));
            const accessToken = params.get('access_token');
            const refreshToken = params.get('refresh_token');
            const err = params.get('error_description')
                || new URLSearchParams(window.location.search).get('error_description');

            const fail = (msg) =>
                window.location.href = "{{ route('login') }}?error=" + encodeURIComponent(msg);

            if (err || !accessToken) return fail(err || 'Login Google gagal.');

            try {
                const res = await fetch("{{ route('auth.session') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ access_token: accessToken, refresh_token: refreshToken }),
                });
                const json = await res.json();
                if (!res.ok) return fail(json.message || 'Login Google gagal.');
                window.location.href = json.redirect;
            } catch (e) {
                fail('Tidak dapat terhubung ke server.');
            }
        })();
    </script>
</body>

</html>