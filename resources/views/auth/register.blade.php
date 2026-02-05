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
                    <p class="small fw-semibold text-muted mb-2">Account</p>
                    <div class="mb-3">
                        <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name') }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email') }}" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                        <input type="password" name="password" id="password" class="form-control" required>
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
                    </div>
                    <p class="small fw-semibold text-muted mb-2 pt-2 border-top">Optional personnel metadata</p>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="employee_id" class="form-label">Employee ID</label>
                            <input type="text" name="employee_id" id="employee_id" class="form-control" value="{{ old('employee_id') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="designation" class="form-label">Designation</label>
                            <input type="text" name="designation" id="designation" class="form-control" value="{{ old('designation') }}">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="department" class="form-label">Department</label>
                        <input type="text" name="department" id="department" class="form-control" value="{{ old('department') }}">
                    </div>
                    <div class="mb-3">
                        <label for="school" class="form-label">School / Office</label>
                        <input type="text" name="school" id="school" class="form-control" value="{{ old('school') }}">
                    </div>
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
@endsection
