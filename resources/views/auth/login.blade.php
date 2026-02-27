@extends('layouts.app')

@section('title', 'Login - ' . config('app.name'))

@push('styles')
<style>
    /* Break out of .container and fill viewport for centering */
    .login-viewport {
        width: 100vw;
        margin-left: calc(-50vw + 50%);
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px 0;
        background: #f1f5f9;
    }
    .login-card {
        min-height: 600px;
        background: rgba(255, 255, 255, 0.98);
        border-radius: 20px;
        backdrop-filter: blur(20px);
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        border: 1px solid rgba(255, 255, 255, 0.8);
        overflow: hidden;
    }
    .login-card .row {
        min-height: 600px;
    }
    /* Left panel – branding */
    .login-brand {
        background: linear-gradient(135deg, #1e293b 0%, #334155 50%, #475569 100%);
        position: relative;
        padding: 60px 40px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 600px;
    }
    .login-brand::before {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(ellipse 80% 50% at 20% 80%, rgba(220, 38, 38, 0.12) 0%, transparent 50%);
        pointer-events: none;
    }
    .login-brand::after {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(ellipse 60% 40% at 80% 20%, rgba(245, 158, 11, 0.08) 0%, transparent 50%);
        pointer-events: none;
    }
    .login-brand .brand-inner {
        position: relative;
        z-index: 1;
        text-align: center;
    }
    .login-brand .logo-wrap {
        margin-bottom: 1.25rem;
    }
    .login-brand .logo-wrap img {
        max-width: 380px;
        width: 100%;
        aspect-ratio: 1 / 1;
        object-fit: contain;
        border-radius: 50%;
        filter: drop-shadow(0 8px 24px rgba(0, 0, 0, 0.35));
    }
    .login-brand .brand-title {
        color: #fff;
        font-size: 1.75rem;
        font-weight: 700;
        margin-bottom: 0.35rem;
    }
    .login-brand .brand-subtitle {
        color: rgba(255, 255, 255, 0.85);
        font-size: 0.95rem;
    }
    /* Right panel – form */
    .login-form-wrap {
        padding: 50px 40px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        min-height: 600px;
    }
    .login-form-inner {
        max-width: 400px;
        margin: 0 auto;
        width: 100%;
    }
    .login-form-wrap .form-heading {
        color: #0f172a;
        font-size: 1.75rem;
        font-weight: 700;
        margin-bottom: 0.35rem;
        text-align: center;
    }
    .login-form-wrap .form-subtext {
        color: #64748b;
        font-size: 0.9rem;
        margin-bottom: 1.75rem;
        text-align: center;
    }
    .login-form-wrap .form-label {
        font-size: 0.9375rem;
        color: #334155;
    }
    .login-form-wrap .form-control {
        height: 50px;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        font-size: 0.9375rem;
    }
    .login-form-wrap .form-control:focus {
        border-color: #1E35FF;
        box-shadow: 0 0 0 3px rgba(30, 53, 255, 0.1);
        outline: 0;
    }
    .login-form-wrap .input-icon-wrap {
        position: relative;
    }
    .login-form-wrap .input-icon-wrap .form-control {
        padding-left: 48px;
    }
    .login-form-wrap .input-icon-wrap .input-icon-left {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #64748b;
        font-size: 1.2rem;
        pointer-events: none;
    }
    .login-form-wrap .password-toggle-wrap {
        position: relative;
    }
    .login-form-wrap .password-toggle-wrap .form-control {
        padding-right: 48px;
    }
    .login-form-wrap .password-toggle-wrap.input-icon-wrap .form-control {
        padding-left: 48px;
        padding-right: 48px;
    }
    .login-form-wrap .password-toggle-btn {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        width: 36px;
        height: 36px;
        padding: 0;
        border: 0;
        background: transparent;
        color: #64748b;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: color 0.2s;
    }
    .login-form-wrap .password-toggle-btn:hover {
        color: #334155;
    }
    .login-form-wrap .password-toggle-btn i {
        font-size: 1.2rem;
    }
    .login-form-wrap .btn-primary-login {
        min-height: 44px;
        padding: 12px 16px;
        border-radius: 10px;
        background: linear-gradient(180deg, #1E35FF 0%, #1E35FF 100%);
        background-color: #1E35FF;
        border: 0;
        color: #fff;
        font-weight: 600;
        font-size: 0.9375rem;
        box-shadow: 0 4px 14px rgba(30, 53, 255, 0.4);
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .login-form-wrap .btn-primary-login:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(30, 53, 255, 0.45);
        color: #fff;
    }
    .login-form-wrap .form-check-input:checked {
        background-color: #1E35FF;
        border-color: #1E35FF;
    }
    .login-form-wrap .divider {
        border-color: #e2e8f0;
        margin: 1rem 0;
    }
    .login-form-wrap .footer-text {
        color: #94a3b8;
        font-size: 0.9375rem;
    }
    .login-form-wrap .footer-text a {
        color: #1E35FF;
        text-decoration: none;
        font-weight: 500;
    }
    .login-form-wrap .footer-text a:hover {
        text-decoration: underline;
    }
    /* Responsive: below 992px – single column, stacked */
    @media (max-width: 991.98px) {
        .login-card {
            min-height: auto;
        }
        .login-card .row {
            min-height: auto;
            flex-direction: column;
        }
        .login-brand {
            min-height: 350px;
            padding: 40px 30px;
        }
        .login-brand .logo-wrap img {
            max-width: 280px;
        }
        .login-form-wrap {
            min-height: auto;
            padding: 40px 30px;
        }
    }
    /* Responsive: below 576px */
    @media (max-width: 575.98px) {
        .login-brand {
            min-height: 300px;
            padding: 30px 20px;
        }
        .login-brand .logo-wrap img {
            max-width: 220px;
        }
        .login-form-wrap {
            padding: 30px 20px;
        }
        .login-form-wrap .form-heading {
            font-size: 1.5rem;
        }
    }
</style>
@endpush

@section('content')
<div class="login-viewport">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-10 col-xl-9">
                <div class="card login-card border-0">
                    <div class="row g-0">
                        <div class="col-lg-5">
                            <div class="login-brand">
                                <div class="brand-inner">
                                    <div class="logo-wrap">
                                        <img src="{{ asset('images/deped-maasin-logo.png') }}" alt="{{ config('app.name') }}">
                                    </div>
                                    <h1 class="brand-title">Maasin City Division</h1>
                                    <p class="brand-subtitle">Information System</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-7">
                            <div class="login-form-wrap">
                                <div class="login-form-inner">
                                    <div class="text-center mb-3">
                                        <img src="{{ asset('images/deped-logo.png') }}" alt="DepEd Logo" style="max-width: 80px; height: auto;">
                                    </div>
                                    <h2 class="form-heading">Welcome back</h2>
                                    <p class="form-subtext">Enter your credentials to continue.</p>
                                    <form method="POST" action="{{ route('login') }}">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="email" class="form-label">Email</label>
                                            <div class="input-icon-wrap">
                                                <i class="bi bi-envelope input-icon-left" aria-hidden="true"></i>
                                                <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror"
                                                       value="{{ old('email') }}" required autofocus>
                                            </div>
                                            @error('email')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label for="password" class="form-label">Password</label>
                                            <div class="password-toggle-wrap input-icon-wrap">
                                                <i class="bi bi-lock input-icon-left" aria-hidden="true"></i>
                                                <input type="password" name="password" id="password" class="form-control" required>
                                                <button type="button" class="password-toggle-btn" id="passwordToggle" aria-label="Toggle password visibility" title="Show password">
                                                    <i class="bi bi-eye" id="passwordToggleIcon" aria-hidden="true"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="mb-4 form-check">
                                            <input type="checkbox" name="remember" id="remember" class="form-check-input">
                                            <label for="remember" class="form-check-label" style="font-size: 0.9375rem;">Remember me</label>
                                        </div>
                                        <button type="submit" class="btn btn-primary-login w-100">Sign In</button>
                                    </form>
                                    <hr class="divider">
                                    <p class="text-center footer-text mb-0">
                                        <a href="{{ route('register') }}">Register as personnel</a>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var btn = document.getElementById('passwordToggle');
    var input = document.getElementById('password');
    var icon = document.getElementById('passwordToggleIcon');
    if (btn && input && icon) {
        btn.addEventListener('click', function() {
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
                btn.setAttribute('title', 'Hide password');
                btn.setAttribute('aria-label', 'Hide password');
            } else {
                input.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
                btn.setAttribute('title', 'Show password');
                btn.setAttribute('aria-label', 'Toggle password visibility');
            }
        });
    }
});
</script>
@endpush
