@extends('layouts.app')

@section('title', 'Edit Profile - ' . config('app.name'))

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <h1 class="page-title mb-0">Edit Profile</h1>
    <a href="{{ route('profile.password') }}" class="btn btn-outline-primary">
        <i class="bi bi-key me-1"></i> Change Password
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <p class="text-muted small mb-4">Update your personal information. Email must be unique.</p>
                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $user->name) }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email', $user->email) }}" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <p class="small fw-semibold text-muted mb-2 pt-2 border-top">Personnel metadata</p>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="employee_id" class="form-label">Employee ID</label>
                            <input type="text" name="employee_id" id="employee_id" class="form-control @error('employee_id') is-invalid @enderror"
                                   value="{{ old('employee_id', $user->employee_id) }}">
                            @error('employee_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="designation" class="form-label">Position / Designation</label>
                            <input type="text" name="designation" id="designation" class="form-control @error('designation') is-invalid @enderror"
                                   value="{{ old('designation', $user->designation) }}">
                            @error('designation')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="school" class="form-label">School / Office</label>
                        <input type="text" name="school" id="school" class="form-control @error('school') is-invalid @enderror"
                               value="{{ old('school', $user->school) }}">
                        @error('school')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="theme" class="form-label">Theme</label>
                        <select name="theme" id="theme" class="form-select @error('theme') is-invalid @enderror">
                            <option value="default" {{ old('theme', $user->theme) == 'default' ? 'selected' : '' }}>Default</option>
                            <option value="red" {{ old('theme', $user->theme) == 'red' ? 'selected' : '' }}>Red</option>
                            <option value="green" {{ old('theme', $user->theme) == 'green' ? 'selected' : '' }}>Green</option>
                        </select>
                        @error('theme')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="d-flex gap-2 pt-2">
                        <button type="submit" class="btn btn-deped">Save changes</button>
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
