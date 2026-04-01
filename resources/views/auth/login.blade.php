<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EMS</title>
   
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        :root {
            --primary-ems: #2563eb;
            --primary-hover: #1d4ed8;
            --bg-body: #f1f5f9;
            --text-main: #0f172a;
            --text-muted: #64748b;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-body);
            background-image: radial-gradient(circle at top right, #e0e7ff, transparent 400px), radial-gradient(circle at bottom left, #dbeafe, transparent 400px);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-main);
            margin: 0;
            padding: 20px;
        }

        .login-container {
            width: 100%;
            max-width: 360px;
        }

        .login-card {
            background: #ffffff;
            border: none;
            border-radius: 16px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            padding: 24px;
        }

        .login-header {
            text-align: center;
            margin-bottom: 18px;
        }

        .login-header h4 {
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.5px;
            margin-bottom: 2px;
            font-size: 1.25rem;
        }

        .login-header p {
            font-size: 0.8rem;
            color: var(--text-muted);
            margin-bottom: 0;
        }

        .form-label {
            font-size: 0.75rem;
            font-weight: 600;
            color: #475569;
            margin-bottom: 6px;
        }

        .input-group-custom {
            margin-bottom: 12px;
        }

        .form-control {
            border-radius: 8px;
            padding: 10px 14px;
            border: 1px solid #cbd5e1;
            font-size: 0.85rem;
            background-color: #f8fafc;
            color: #1e293b;
            transition: all 0.2s ease;
        }

        .form-control:focus {
            background-color: #ffffff;
            border-color: var(--primary-ems);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
            outline: none;
        }

        /* Hide number input spinners */
        input[type="number"]::-webkit-outer-spin-button,
        input[type="number"]::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        input[type="number"] {
            -moz-appearance: textfield;
        }

        /* Role Selector Styles */
        .role-selector .btn-outline-primary {
            border-color: #e2e8f0;
            color: #64748b;
            background-color: #f8fafc;
            border-radius: 8px;
            transition: all 0.2s ease;
            box-shadow: 0 1px 2px rgba(0,0,0,0.02);
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .role-selector .btn-check:checked + .btn-outline-primary {
            background-color: #eff6ff;
            color: var(--primary-ems);
            border-color: #bfdbfe;
            box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.1);
            transform: translateY(-1px);
        }

        .role-selector .btn-outline-primary:hover {
            background-color: #f1f5f9;
            border-color: #cbd5e1;
        }

        .btn-ems {
            background-color: var(--primary-ems);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 10px;
            font-weight: 600;
            font-size: 0.85rem;
            width: 100%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            margin-top: 20px;
            transition: all 0.2s ease;
            box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2);
        }

        .btn-ems:hover {
            background-color: var(--primary-hover);
            transform: translateY(-1px);
            box-shadow: 0 6px 8px -1px rgba(37, 99, 235, 0.3);
        }

        .alert-modern {
            background-color: #fef2f2;
            border: 1px solid #fee2e2;
            color: #991b1b;
            border-radius: 8px;
            padding: 8px 12px;
            font-size: 0.75rem;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .footer-text {
            text-align: center;
            margin-top: 20px;
            font-size: 0.7rem;
            color: #94a3b8;
            font-weight: 500;
        }

        .ems-brand-logo {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--primary-ems), #0ea5e9);
            color: white;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            margin: 0 auto 12px;
            box-shadow: 0 4px 10px rgba(37, 99, 235, 0.3);
        }

    </style>
</head>

<body>
    <div class="login-container">
        <div class="card login-card">
            <div class="login-header">
                <div class="ems-brand-logo">
                    <i class="bi bi-shield-check"></i>
                </div>
                <p>ESD Management System (EMS)</p>
            </div>

            <div class="card-body p-0">
                @if($errors->any())
                    <div id="auth-alert" class="alert-modern alert-danger">
                        <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                        <span id="auth-message">
                            {{ $errors->first() }}
                            @if(session('lockout_seconds'))
                                <br>Silakan tunggu <strong id="auth-timer">{{ session('lockout_seconds') }}</strong> detik.
                            @endif
                        </span>
                    </div>
                @endif

                <form id="login-form" method="POST" action="{{ route('login') }}">
                    @csrf
                    <input type="hidden" name="encrypted_username" id="encrypted_username">
                    <input type="hidden" name="encrypted_password" id="encrypted_password">
               
                    <!-- Role Selection -->
                    <label class="form-label mb-2">Login Sebagai</label>
                    <div class="role-selector d-flex justify-content-between gap-1 mb-3">
                        <input type="radio" class="btn-check" name="role" id="role-admin" autocomplete="off" value="admin" checked>
                        <label class="btn btn-outline-primary w-100 py-1 px-1 text-center" for="role-admin">
                            <i class="bi bi-person-workspace d-block mb-0 fs-6"></i>
                            Admin
                        </label>
                        
                        <input type="radio" class="btn-check" name="role" id="role-karyawan" autocomplete="off" value="employee">
                        <label class="btn btn-outline-primary w-100 py-1 px-1 text-center" for="role-karyawan">
                            <i class="bi bi-person-badge d-block mb-0 fs-6"></i>
                            Karyawan
                        </label>

                        <input type="radio" class="btn-check" name="role" id="role-vendor" autocomplete="off" value="vendor">
                        <label class="btn btn-outline-primary w-100 py-1 px-1 text-center" for="role-vendor">
                            <i class="bi bi-shop d-block mb-0 fs-6"></i>
                            Laundry
                        </label>
                    </div>

                    <div class="input-group-custom">
                        <label for="username" id="username-label" class="form-label">Username</label>
                        <input type="text" id="username" class="form-control" required placeholder="Masukkan Username Admin">
                    </div>

                    <div class="input-group-custom">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" id="password" class="form-control" required placeholder="••••••••">
                    </div>
               
                    <button type="submit" class="btn-ems">
                        Sign In <i class="bi bi-arrow-right-short fs-5"></i>
                    </button>
                </form>
            </div>
        </div>
       
        <div class="footer-text">
            PT Astra Visteon Indonesia &copy; {{ date('Y') }}<br>
            Integrated Asset Management System
        </div>
    </div>

@if(session('lockout_seconds'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let timerElement = document.getElementById('auth-timer');
        let messageElement = document.getElementById('auth-message');
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
        // Add small interactivity: dynamic placeholders and input type based on role
        document.querySelectorAll('input[name="role"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const usernameInput = document.getElementById('username');
                const usernameLabel = document.getElementById('username-label');
                if(this.value === 'admin') {
                    usernameLabel.textContent = 'Username';
                    usernameInput.placeholder = 'Masukkan Username Admin';
                    usernameInput.type = 'text';
                }
                if(this.value === 'employee') {
                    usernameLabel.textContent = 'NPK';
                    usernameInput.placeholder = 'Masukkan NPK Karyawan';
                    usernameInput.type = 'number';
                }
                if(this.value === 'vendor') {
                    usernameLabel.textContent = 'Username';
                    usernameInput.placeholder = 'Masukkan Kode Vendor / Username';
                    usernameInput.type = 'text';
                }
                usernameInput.value = '';
            });
        });

        document.getElementById('login-form').addEventListener('submit', function (e) {
            e.preventDefault();
            
            // -- FRONTEND DUMMY LOGIC FOR VENDOR --
            const selectedRole = document.querySelector('input[name="role"]:checked').value;
            if (selectedRole === 'vendor') {
                const btn = this.querySelector('.btn-ems');
                btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" style="width: 1rem; height: 1rem;"></span> Loading...';
                
                setTimeout(() => {
                    alert('Simulasi Login Vendor Berhasil! Mengalihkan ke Dashboard Vendor...');
                    window.location.href = '/vendor/dashboard';
                }, 800);
                return; // Stop form submission to backend
            }
            // ------------------------------------

            const btn = this.querySelector('.btn-ems');
            btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" style="width: 1rem; height: 1rem;"></span> Loading...';
            btn.style.opacity = '0.8';
            btn.style.pointerEvents = 'none';

            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;
            const publicKey = `{!! isset($publicKey) ? str_replace(["\n", "\r"], ["\\n", ""], $publicKey) : '' !!}`;

            if(publicKey) {
                const encrypt = new JSEncrypt();
                encrypt.setPublicKey(publicKey);

                const encryptedUsername = encrypt.encrypt(username);
                const encryptedPassword = encrypt.encrypt(password);

                if (!encryptedUsername || !encryptedPassword) {
                    alert('Encryption Error. Silakan refresh halaman.');
                    location.reload();
                    return;
                }

                document.getElementById('encrypted_username').value = encryptedUsername;
                document.getElementById('encrypted_password').value = encryptedPassword;
            } else {
                document.getElementById('encrypted_username').value = username;
                document.getElementById('encrypted_password').value = password;
            }

            this.submit();
        });
    </script>
</body>
</html>