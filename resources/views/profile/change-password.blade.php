@extends('layouts.app')

@section('title', 'Change Password - ' . config('app.name'))

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <h1 class="page-title mb-0">Change Password</h1>
    <a href="{{ route('profile.edit') }}" class="btn btn-outline-secondary">
        <i class="bi bi-person me-1"></i> Edit Profile
    </a>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <p class="text-muted small mb-4">Enter your current password and choose a new one. Use a strong password.</p>
                <form method="POST" action="{{ route('profile.password.update') }}">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Current password <span class="text-danger">*</span></label>
                        <div class="password-input-wrapper position-relative">
                            <input type="password" name="current_password" id="current_password" class="form-control @error('current_password') is-invalid @enderror"
                                   autocomplete="current-password" required>
                            <button type="button" class="btn btn-link position-absolute end-0 top-50 translate-middle-y text-muted password-toggle" data-target="current_password"
                                    aria-label="Show password" title="Show password">
                                <i class="bi bi-eye" aria-hidden="true"></i>
                            </button>
                        </div>
                        @error('current_password')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">New password <span class="text-danger">*</span></label>
                        <div class="password-input-wrapper position-relative">
                            <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror"
                                   autocomplete="new-password" required
                                   aria-describedby="password-match-msg{{ $errors->has('password') ? ' password-error' : '' }}">
                            <button type="button" class="btn btn-link position-absolute end-0 top-50 translate-middle-y text-muted password-toggle" data-target="password"
                                    aria-label="Show password" title="Show password">
                                <i class="bi bi-eye" aria-hidden="true"></i>
                            </button>
                        </div>
                        @error('password')<div class="invalid-feedback d-block" id="password-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label">Confirm new password <span class="text-danger">*</span></label>
                        <div class="password-input-wrapper position-relative">
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control"
                                   autocomplete="new-password" required aria-describedby="password-match-msg">
                            <button type="button" class="btn btn-link position-absolute end-0 top-50 translate-middle-y text-muted password-toggle" data-target="password_confirmation"
                                    aria-label="Show password" title="Show password">
                                <i class="bi bi-eye" aria-hidden="true"></i>
                            </button>
                        </div>
                        <div id="password-match-msg" class="form-text small" role="status" aria-live="polite"></div>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-deped">Change password</button>
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.password-input-wrapper .form-control { padding-right: 2.75rem; }
.password-input-wrapper .password-toggle { padding: 0.25rem 0.5rem; margin-right: 0.25rem; }
.password-input-wrapper .form-control.password-match { border-color: #198754; background-color: rgba(25, 135, 84, 0.06); }
.password-input-wrapper .form-control.password-mismatch { border-color: #dc3545; background-color: rgba(220, 53, 69, 0.06); }
#password-match-msg.match-ok { color: #198754; }
#password-match-msg.match-err { color: #dc3545; }
</style>
@push('scripts')
<script>
(function() {
    'use strict';
    function setupToggle(btn) {
        if (!btn || !btn.dataset.target) return;
        var input = document.getElementById(btn.dataset.target);
        if (!input) return;
        btn.addEventListener('click', function() {
            var isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';
            btn.setAttribute('aria-label', isPassword ? 'Hide password' : 'Show password');
            btn.setAttribute('title', isPassword ? 'Hide password' : 'Show password');
            var icon = btn.querySelector('i.bi');
            if (icon) { icon.classList.toggle('bi-eye', !isPassword); icon.classList.toggle('bi-eye-slash', isPassword); }
        });
    }
    document.querySelectorAll('.password-toggle').forEach(setupToggle);

    var password = document.getElementById('password');
    var passwordConfirmation = document.getElementById('password_confirmation');
    var matchMsg = document.getElementById('password-match-msg');
    function checkMatch() {
        if (!password || !passwordConfirmation || !matchMsg) return;
        var p = password.value;
        var c = passwordConfirmation.value;
        password.classList.remove('password-match', 'password-mismatch');
        passwordConfirmation.classList.remove('password-match', 'password-mismatch');
        matchMsg.classList.remove('match-ok', 'match-err');
        matchMsg.textContent = '';
        if (!p && !c) return;
        if (p && c) {
            if (p === c) {
                password.classList.add('password-match');
                passwordConfirmation.classList.add('password-match');
                matchMsg.classList.add('match-ok');
                matchMsg.textContent = 'Passwords match.';
            } else {
                password.classList.add('password-mismatch');
                passwordConfirmation.classList.add('password-mismatch');
                matchMsg.classList.add('match-err');
                matchMsg.textContent = 'Passwords do not match.';
            }
        }
    }
    if (password) password.addEventListener('input', checkMatch);
    if (passwordConfirmation) passwordConfirmation.addEventListener('input', checkMatch);
})();
</script>
@endpush
@endsection
