@extends('layouts.app')

@section('title', 'Login - ' . config('app.name'))

@section('content')
<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card shadow-sm border-0">
            <div class="card-body p-5">
                <div class="text-center mb-4">
                    <img src="{{ asset('images/deped-maasin-logo.png') }}" alt="{{ config('app.name') }}" class="mb-2" style="max-height: 90px; width: auto;">
                    <h5 class="mb-1">Training Tracker</h5>
                    <p class="text-muted small mb-0">Sign in to your account</p>
                </div>
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email') }}" required autofocus>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" name="password" id="password" class="form-control" required>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" name="remember" id="remember" class="form-check-input">
                        <label for="remember" class="form-check-label">Remember me</label>
                    </div>
                    <button type="submit" class="btn btn-deped w-100">Sign In</button>
                </form>
                <p class="text-center mt-3 mb-0 small">
                    <a href="{{ route('register') }}">Register as personnel</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
