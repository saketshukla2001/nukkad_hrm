<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Nukkad HRM - Admin Panel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- Laravel way asset --}}
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body>
    <input type="checkbox" id="sidebar-toggle" class="sidebar-toggle">
    <label for="sidebar-toggle" class="sidebar-overlay" aria-label="Close sidebar"></label>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="brand">
            <span style="display:inline-flex; width:34px; height:34px; border-radius:12px; align-items:center; justify-content:center; background:rgba(79,70,229,0.18); border:1px solid rgba(255,255,255,0.10);">NH</span>
            <div style="display:flex; flex-direction:column; line-height:1.1;">
                <span style="font-size:14px; opacity:0.85; font-weight:700;">Admin</span>
                <span>Nukkad HRM</span>
            </div>
        </div>

        <div class="sidebar-section">
            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                🧭 Dashboard
            </a>
        </div>

        <!-- Candidates Menu -->
        <div class="menu-group">
            <div class="menu-title">Candidates</div>

            <a href="{{ route('admin.candidates.index') }}" class="{{ request()->routeIs('admin.candidates.index') ? 'active' : '' }}">
                📋 Candidate List
            </a>

            <a href="{{ route('admin.candidates.create') }}" class="{{ request()->routeIs('admin.candidates.create') ? 'active' : '' }}">
                ➕ Add Candidate
            </a>
        </div>

        <!-- Offer Letter Menu -->
        <div class="menu-group">
            <div class="menu-title">Offer Letters</div>

            <a href="{{ route('admin.offerletter.template.edit') }}" class="{{ request()->routeIs('admin.offerletter.template.*') ? 'active' : '' }}">
                ✏️ Edit Offer Template
            </a>
        </div>

        <!-- <div class="menu-group">
            <div class="menu-title">More</div>
            <a href="#">👥 Employees</a>
            <a href="{{ route('admin.settings.index') }}" class="{{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">⚙️ Settings</a>
        </div> -->

        <div class="sidebar-bottom">
            <a href="{{ route('admin.logout') }}">🚪 Logout</a>
        </div>
    </div>

    <!-- Main -->
    <div class="main">
        <!-- Topbar -->
        <div class="topbar">
            <div style="display:flex; align-items:center; gap:12px;">
                <label for="sidebar-toggle" class="sidebar-toggle-btn" aria-label="Open sidebar">☰</label>
                <div class="title">Nukkad HRM</div>
            </div>
            <div class="user">Welcome, Admin</div>
        </div>

        <!-- Page Content -->
        <div class="content">
            @yield('content')
        </div>
    </div>

</body>
</html>
