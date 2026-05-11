@extends('admin.layout')

@section('content')
<div class="card" style="margin-bottom:14px;">
    <div class="page-header" style="margin-bottom:0;">
        <div>
            <h2 style="margin:0;">Settings</h2>
            <p class="subtitle">Manage your admin profile and password.</p>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert">
        {{ session('success') }}
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger">
        <ul style="margin:0; padding-left:20px;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="settings-grid">
    <div class="card">
        <div class="page-header">
            <div>
                <h2 style="margin:0;">Profile</h2>
                <p class="subtitle">Update the admin name and login email.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.settings.profile.update') }}">
            @csrf

            <div class="form-grid">
                <div class="field">
                    <label>Name</label>
                    <input type="text" name="name" value="{{ old('name', $admin->name) }}" placeholder="Enter admin name" required>
                </div>

                <div class="field">
                    <label>Email</label>
                    <input type="email" name="email" value="{{ old('email', $admin->email) }}" placeholder="Enter login email" required>
                </div>
            </div>

            <div style="margin-top: 18px;">
                <button type="submit" class="btn-primary">💾 Save Profile</button>
            </div>
        </form>
    </div>

    <div class="card">
        <div class="page-header">
            <div>
                <h2 style="margin:0;">Change Password</h2>
                <p class="subtitle">Use a strong password with at least 8 characters.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.settings.password.update') }}">
            @csrf

            <div class="form-grid">
                <div class="field span-2">
                    <label>Current Password</label>
                    <input type="password" name="current_password" placeholder="Enter current password" required>
                </div>

                <div class="field">
                    <label>New Password</label>
                    <input type="password" name="password" placeholder="Enter new password" required>
                </div>

                <div class="field">
                    <label>Confirm Password</label>
                    <input type="password" name="password_confirmation" placeholder="Confirm new password" required>
                </div>
            </div>

            <div style="margin-top: 18px;">
                <button type="submit" class="btn-primary">🔒 Update Password</button>
            </div>
        </form>
    </div>
</div>
@endsection
