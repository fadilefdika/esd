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

        #togglePassword {
            color: #6c757d; /* Warna abu-abu muted agar tidak terlalu mencolok */
            transition: color 0.2s;
        }

        #togglePassword:hover {
            color: #0d6efd; /* Berubah jadi biru saat dihover */
        }

        /* Pastikan input password tidak menimpa ikon */
        #password {
            padding-right: 45px !important;
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
                    <input type="hidden" name="role" id="hidden_role" value="admin">
               
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
                        <input type="text" name="username" id="username" class="form-control" required placeholder="Masukkan Username Admin">
                    </div>

                    <div class="input-group-custom">
                        <label for="password" class="form-label">Password</label>
                        <div class="position-relative">
                            <input type="password" name="password" id="password" class="form-control" required placeholder="••••••••" style="padding-right: 40px;">
                            <span id="togglePassword" class="position-absolute end-0 top-50 translate-middle-y me-3" style="cursor: pointer; z-index: 10;">
                                <i class="bi bi-eye" id="eyeIcon"></i>
                            </span>
                        </div>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // === 1. Logika Lockout Timer ===
    @if(session('lockout_seconds'))
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
                if(messageElement) messageElement.innerHTML = 'Silakan masukkan username dan password';
                if (submitBtn) {
                    submitBtn.style.opacity = '1';
                    submitBtn.style.pointerEvents = 'auto';
                }
            } else {
                if (timerElement) timerElement.innerText = seconds;
            }
        }, 1000);
    @endif

    // === 2. Toggle Visibility Password ===
    const togglePassword = document.querySelector('#togglePassword');
    const passwordInput = document.querySelector('#password');
    const eyeIcon = document.querySelector('#eyeIcon');

    if (togglePassword && passwordInput) {
        togglePassword.addEventListener('click', function () {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            // Toggle ikon mata versi Bootstrap
            eyeIcon.classList.toggle('bi-eye');
            eyeIcon.classList.toggle('bi-eye-slash');
        });
    }

    // === 3. Dynamic UI Based on Role (NPK vs Username) ===
    document.querySelectorAll('input[name="role"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const usernameInput = document.getElementById('username');
            const usernameLabel = document.getElementById('username-label');
            const hiddenRoleInput = document.getElementById('hidden_role');
            
            if(!usernameInput || !usernameLabel) return;

            if(this.value === 'employee') {
                usernameLabel.textContent = 'NPK';
                usernameInput.placeholder = 'Masukkan NPK Karyawan';
                usernameInput.type = 'number'; // NPK biasanya angka
            } else {
                usernameLabel.textContent = 'Username';
                usernameInput.placeholder = this.value === 'admin' ? 'Masukkan Username Admin' : 'Masukkan Username';
                usernameInput.type = 'text';
            }
            usernameInput.value = '';
            if(hiddenRoleInput) hiddenRoleInput.value = this.value;
        });
    });

    // === 4. Form Submission Loading Effect ===
    const loginForm = document.getElementById('login-form');
    if (loginForm) {
        loginForm.addEventListener('submit', function (e) {
            const btn = this.querySelector('.btn-ems');
            if(btn) {
                btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Loading...';
                btn.style.opacity = '0.8';
                btn.style.pointerEvents = 'none';
            }
        });
    }
});
</script>
</body>
</html>