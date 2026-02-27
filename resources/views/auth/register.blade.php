@extends('layouts.app')

@section('title', 'Register - ' . config('app.name'))

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm border-0">
            <div class="card-body p-5">
                <div class="text-center mb-4">
                    <h5 class="mb-1">{{ isset($forAdmin) && $forAdmin ? 'Add personnel' : config('app.name') }}</h5>
                    <p class="text-muted small mb-0">{{ isset($forAdmin) && $forAdmin ? 'Create a new personnel account' : 'Register as personnel / teacher' }}</p>
                </div>
                <form method="POST" action="{{ isset($forAdmin) && $forAdmin ? route('personnel.store') : route('register') }}">
                    @csrf
                    <p class="small fw-semibold text-muted mb-2">Account — Name (reflects in PDS)</p>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="surname" class="form-label">Surname <span class="text-danger">*</span></label>
                            <input type="text" name="surname" id="surname" class="form-control @error('surname') is-invalid @enderror"
                                   value="{{ old('surname') }}" maxlength="100" required>
                            @error('surname')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                            <input type="text" name="first_name" id="first_name" class="form-control @error('first_name') is-invalid @enderror"
                                   value="{{ old('first_name') }}" maxlength="100" required>
                            @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="middle_name" class="form-label">Middle Name</label>
                            <input type="text" name="middle_name" id="middle_name" class="form-control @error('middle_name') is-invalid @enderror"
                                   value="{{ old('middle_name') }}" maxlength="100">
                            @error('middle_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="name_extension" class="form-label">Name Extension</label>
                            <input type="text" name="name_extension" id="name_extension" class="form-control @error('name_extension') is-invalid @enderror"
                                   value="{{ old('name_extension') }}" maxlength="20" placeholder="e.g. JR">
                            @error('name_extension')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email') }}" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                        <div class="password-input-wrapper position-relative">
                            <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror"
                                   autocomplete="new-password" required
                                   aria-describedby="password-match-msg{{ $errors->has('password') ? ' password-error' : '' }}"
                                   aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}">
                            <button type="button" class="btn btn-link position-absolute end-0 top-50 translate-middle-y text-muted password-toggle" data-target="password"
                                    aria-label="Show password" title="Show password">
                                <i class="bi bi-eye" aria-hidden="true"></i>
                            </button>
                        </div>
                        @error('password')<div class="invalid-feedback d-block" id="password-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                        <div class="password-input-wrapper position-relative">
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control"
                                   autocomplete="new-password" required
                                   aria-describedby="password-match-msg"
                                   aria-invalid="false">
                            <button type="button" class="btn btn-link position-absolute end-0 top-50 translate-middle-y text-muted password-toggle" data-target="password_confirmation"
                                    aria-label="Show password" title="Show password">
                                <i class="bi bi-eye" aria-hidden="true"></i>
                            </button>
                        </div>
                        <div id="password-match-msg" class="form-text small" role="status" aria-live="polite"></div>
                    </div>
                    <p class="small fw-semibold text-muted mb-2 pt-2 border-top">Personnel metadata</p>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="employee_id" class="form-label">Employee ID <span class="text-danger">*</span></label>
                            <input type="text" name="employee_id" id="employee_id" class="form-control @error('employee_id') is-invalid @enderror"
                                   value="{{ old('employee_id') }}" required aria-required="true">
                            @error('employee_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="designation" class="form-label">Position</label>
                            <input type="text" name="designation" id="designation" class="form-control" value="{{ old('designation') }}">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="school" class="form-label">School / Office</label>
                        <input type="text" name="school" id="school" class="form-control" value="{{ old('school') }}">
                    </div>
                    
                    @if(isset($forAdmin) && $forAdmin)
                    <div class="mb-3">
                        <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                        @php
                            $currentUser = auth()->user();
                        @endphp
                        @if($currentUser && $currentUser->isAdmin())
                        <select name="role" id="role" class="form-select @error('role') is-invalid @enderror" required>
                            <option value="" {{ old('role') ? '' : 'selected' }}>Select a role</option>
                            <option value="personnel" {{ old('role') === 'personnel' ? 'selected' : '' }}>Personnel</option>
                            <option value="sub-admin" {{ old('role') === 'sub-admin' ? 'selected' : '' }}>Sub-Admin</option>
                        </select>
                        @else
                        <!-- Sub-admins can only create personnel, so role is fixed -->
                        <select name="role" id="role" class="form-select" required readonly>
                            <option value="personnel" selected>Personnel</option>
                        </select>
                        <small class="text-muted">Sub-admins can only create personnel accounts</small>
                        @endif
                        @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    @endif
                    <button type="submit" class="btn btn-deped w-100">{{ isset($forAdmin) && $forAdmin ? 'Add personnel' : 'Register' }}</button>
                </form>
                <p class="text-center mt-3 mb-0 small">
                    @if(isset($forAdmin) && $forAdmin)
                        <a href="{{ route('personnel.index') }}">Back to personnel</a>
                    @else
                        <a href="{{ route('login') }}">Already have an account? Sign in</a>
                    @endif
                </p>
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

    var password = document.getElementById('password');
    var passwordConfirmation = document.getElementById('password_confirmation');
    var matchMsg = document.getElementById('password-match-msg');
    var form = password && password.closest('form');

    function togglePasswordVisibility(button) {
        var targetId = button.getAttribute('data-target');
        var input = document.getElementById(targetId);
        if (!input) return;
        var icon = button.querySelector('i.bi');
        var isPassword = input.type === 'password';
        input.type = isPassword ? 'text' : 'password';
        button.setAttribute('aria-label', isPassword ? 'Hide password' : 'Show password');
        button.setAttribute('title', isPassword ? 'Hide password' : 'Show password');
        if (icon) {
            icon.classList.remove(isPassword ? 'bi-eye' : 'bi-eye-slash');
            icon.classList.add(isPassword ? 'bi-eye-slash' : 'bi-eye');
        }
    }

    function checkPasswordMatch() {
        if (!password || !passwordConfirmation || !matchMsg) return;
        var p = password.value;
        var c = passwordConfirmation.value;
        password.classList.remove('password-match', 'password-mismatch');
        passwordConfirmation.classList.remove('password-match', 'password-mismatch');
        matchMsg.classList.remove('match-ok', 'match-err');
        matchMsg.textContent = '';
        if (c.length === 0) {
            passwordConfirmation.setAttribute('aria-invalid', 'false');
            if (p.length > 0) {
                password.classList.remove('password-match', 'password-mismatch');
            }
            return;
        }
        if (p === c) {
            password.classList.add('password-match');
            passwordConfirmation.classList.add('password-match');
            matchMsg.classList.add('match-ok');
            matchMsg.textContent = 'Passwords match.';
            passwordConfirmation.setAttribute('aria-invalid', 'false');
        } else {
            password.classList.add('password-mismatch');
            passwordConfirmation.classList.add('password-mismatch');
            matchMsg.classList.add('match-err');
            matchMsg.textContent = 'Passwords do not match.';
            passwordConfirmation.setAttribute('aria-invalid', 'true');
        }
    }

    function updateSubmitState() {
        if (!form) return;
        var p = password.value;
        var c = passwordConfirmation.value;
        var submitBtn = form.querySelector('button[type="submit"]');
        if (!submitBtn) return;
        var mismatch = c.length > 0 && p !== c;
        submitBtn.disabled = !!mismatch;
    }

    document.querySelectorAll('.password-toggle').forEach(function(btn) {
        btn.addEventListener('click', function() { togglePasswordVisibility(btn); });
    });

    if (password) password.addEventListener('input', function() { checkPasswordMatch(); updateSubmitState(); });
    if (passwordConfirmation) passwordConfirmation.addEventListener('input', function() { checkPasswordMatch(); updateSubmitState(); });

    checkPasswordMatch();
    updateSubmitState();
})();
</script>
@endpush
@endsection
