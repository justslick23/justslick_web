@extends('layouts.admin')

@section('title', 'Log in')

@section('content')
    <div class="login-card">
        <h1 class="h3 mb-4">Admin login</h1>

        <form method="POST" action="{{ route('admin.login') }}" class="admin-card">
            @csrf

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    class="form-control @error('email') is-invalid @enderror"
                    autocomplete="username"
                    autofocus
                    required
                    @error('email') aria-describedby="email-error" @enderror
                >
                @error('email')
                    <div id="email-error" class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label for="password" class="form-label">Password</label>
                <input
                    id="password"
                    type="password"
                    name="password"
                    class="form-control @error('password') is-invalid @enderror"
                    autocomplete="current-password"
                    required
                    @error('password') aria-describedby="password-error" @enderror
                >
                @error('password')
                    <div id="password-error" class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-brand w-100">Log in</button>
        </form>
    </div>
@endsection