<!DOCTYPE html>

<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EMS - ESD Management System</title>
   
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --primary-ems: #2563eb;
            --primary-hover: #1d4ed8;
            --bg-body: #f8fafc;
            --text-main: #0f172a;
            --text-muted: #64748b;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-body);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-main);
            margin: 0;
            padding: 10px;
        }

        /* Container diperkecil dari 400px ke 340px */
        .login-container {
            width: 100%;
            max-width: 340px;
        }

        /* Ukuran Brand diperkecil */
        .ems-brand {
            font-size: 1.5rem;
            font-weight: 800;
            letter-spacing: -1px;
            color: #0f172a;
            margin-bottom: 0;
        }

        .ems-brand span {
            color: var(--primary-ems);
        }

        .login-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px; /* Lebih kotak sedikit agar compact */
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            margin-top: 15px;
        }

        .login-header {
            padding: 20px 20px 0;
            text-align: center; /* Ubah ke center agar seimbang di ukuran kecil */
        }

        .login-header h5 {
            font-weight: 700;
            font-size: 1rem; /* Lebih kecil */
            margin: 0;
        }

        .card-body {
            padding: 16px 20px 20px;
        }

        .form-label {
            font-size: 0.75rem; /* Ukuran micro-label */
            font-weight: 600;
            color: #64748b;
            margin-bottom: 4px;
            display: block;
        }

        .input-group-custom {
            margin-bottom: 12px; /* Jarak antar input lebih rapat */
        }

        .form-control {
            width: 100%;
            box-sizing: border-box;
            border-radius: 6px;
            padding: 8px 12px; /* Padding lebih tipis */
            border: 1px solid #cbd5e1;
            font-size: 0.85rem;
            transition: all 0.2s;
        }

        .form-control:focus {
            border-color: var(--primary-ems);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
            outline: none;
        }

        .btn-ems {
            background-color: var(--primary-ems);
            color: white;
            border: none;
            border-radius: 6px;
            padding: 10px;
            font-weight: 600;
            font-size: 0.85rem;
            width: 100%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            margin-top: 5px;
        }

        .btn-ems:hover {
            background-color: var(--primary-hover);
        }

        .alert-modern {
            background-color: #fef2f2;
            border: 1px solid #fee2e2;
            color: #991b1b;
            border-radius: 6px;
            padding: 8px 12px;
            font-size: 0.75rem;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .footer-text {
            text-align: center;
            margin-top: 16px;
            font-size: 0.7rem;
            color: var(--text-muted);
        }

        .ems-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            background: #f1f5f9;
            padding: 4px 10px;
            border-radius: 4px;
            margin-top: 15px;
            font-weight: 600;
            font-size: 0.65rem;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Sembunyikan deskripsi di mobile yang sangat kecil agar tetap muat */

        @media (max-height: 500px) {
            .footer-text, .ems-badge { display: none; }
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="card login-card">
            <div class="login-header">
                <h5>Authentication</h5>
            </div>

            <div class="card-body">
                @if($errors->any())
                    <div id="auth-alert" class="alert-modern alert-danger d-flex align-items-center mb-3" style="gap: 8px;">
                        <i class="bi bi-exclamation-circle-fill"></i>
                        <span id="auth-message">
                            {{ $errors->first() }}
                            @if(session('lockout_seconds'))
                                Silakan tunggu <span id="auth-timer" style="font-weight: 700;">{{ session('lockout_seconds') }}</span> detik.
                            @endif
                        </span>
                    </div>
                @endif

                <form id="login-form" method="POST" action="{{ route('login') }}">
                    @csrf
                    <input type="hidden" name="encrypted_username" id="encrypted_username">
                    <input type="hidden" name="encrypted_password" id="encrypted_password">
               
                    <div class="input-group-custom">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" id="username" class="form-control" required placeholder="Input Username">
                    </div>

                    <div class="input-group-custom">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" id="password" class="form-control" required placeholder="••••••••">
                    </div>
               
                    <button type="submit" class="btn-ems">
                        <span>Sign In</span>
                        <i class="bi bi-arrow-right-short" style="font-size: 1.1rem;"></i>
                    </button>
                </form>

                <div class="text-center">
                    <div class="ems-badge">
                        <i class="bi bi-patch-check-fill" style="color: #0ea5e9;"></i>
                        System Verified
                    </div>
                </div>
            </div>
        </div>
       
        <div class="footer-text">
            PT Astra Visteon Indonesia<br>
            {{ date('Y') }} • Integrated Asset Management
        </div>
    </div>

@if(session('lockout_seconds'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let timerElement = document.getElementById('auth-timer');
        let messageElement = document.getElementById('auth-message');
        let alertElement = document.getElementById('auth-alert');
        let submitBtn = document.querySelector('.btn-ems');
        let seconds = parseInt("{{ session('lockout_seconds') }}");

        if (submitBtn) {
            submitBtn.style.opacity = '0.5';
            submitBtn.style.pointerEvents = 'none';
        }

        let countdown = setInterval(function() {
            seconds--;
            
            if (seconds <= 0) {
                clearInterval(countdown);
                messageElement.innerHTML = 'Silakan masukkan username dan password';
                
                if (submitBtn) {
                    submitBtn.style.opacity = '1';
                    submitBtn.style.pointerEvents = 'auto';
                }
            } else {
                if (timerElement) timerElement.innerText = seconds;
            }
        }, 1000);
    });
</script>
@endif
    <script src="https://cdn.jsdelivr.net/npm/jsencrypt@3.0.0-rc.1/bin/jsencrypt.min.js"></script>
    <script>
        document.getElementById('login-form').addEventListener('submit', function (e) {
            e.preventDefault();

            const btn = this.querySelector('.btn-ems');
            btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" style="width: 0.8rem; height: 0.8rem;"></span>';
            btn.style.opacity = '0.7';
            btn.style.pointerEvents = 'none';

            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;
            const publicKey = `{!! str_replace(["\n", "\r"], ["\\n", ""], $publicKey) !!}`;

            const encrypt = new JSEncrypt();
            encrypt.setPublicKey(publicKey);

            const encryptedUsername = encrypt.encrypt(username);
            const encryptedPassword = encrypt.encrypt(password);

            if (!encryptedUsername || !encryptedPassword) {
                alert('Encryption Error');
                location.reload();
                return;
            }

            document.getElementById('encrypted_username').value = encryptedUsername;
            document.getElementById('encrypted_password').value = encryptedPassword;

            this.submit();
        });
    </script>
</body>

</html>