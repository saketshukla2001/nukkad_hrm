<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Nukkad HRM - Admin Panel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body>
    <input type="checkbox" id="sidebar-toggle" class="sidebar-toggle">
    <label for="sidebar-toggle" class="sidebar-overlay" aria-label="Close sidebar"></label>

    <aside class="sidebar">
        <div class="brand">
            <span class="brand-logo">NH</span>
            <div class="brand-text">
                <span class="brand-label">Admin Panel</span>
                <span class="brand-name">Nukkad HRM</span>
            </div>
        </div>

        <div class="sidebar-section">
            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <span class="nav-icon">📊</span> Dashboard
            </a>
        </div>

        <div class="menu-group">
            <div class="menu-title">Candidates</div>
            <a href="{{ route('admin.candidates.index') }}" class="{{ request()->routeIs('admin.candidates.index') ? 'active' : '' }}">
                <span class="nav-icon">👥</span> Candidate List
            </a>
            <a href="{{ route('admin.candidates.create') }}" class="{{ request()->routeIs('admin.candidates.create') ? 'active' : '' }}">
                <span class="nav-icon">➕</span> Add Candidate
            </a>
        </div>

        <div class="menu-group">
            <div class="menu-title">Offer Letters</div>
            <a href="{{ route('admin.offerletter.template.edit') }}" class="{{ request()->routeIs('admin.offerletter.template.*') ? 'active' : '' }}">
                <span class="nav-icon">📝</span> Edit Offer Template
            </a>
        </div>

        <div class="sidebar-bottom">
            <a href="{{ route('admin.logout') }}">
                <span class="nav-icon">🚪</span> Logout
            </a>
        </div>
    </aside>

    <div class="main">
        <header class="topbar">
            <div class="topbar-left">
                <label for="sidebar-toggle" class="sidebar-toggle-btn" aria-label="Open sidebar">☰</label>
                <div>
                    <div class="title">Nukkad HRM</div>
                    <div class="page-breadcrumb">Human Resource Management</div>
                </div>
            </div>
            <div class="topbar-right">
                <div class="user-pill">
                    <div class="user-avatar">A</div>
                    <div class="user-info">
                        <div class="user-name">Admin</div>
                        <div class="user-role">Super Administrator</div>
                    </div>
                </div>
            </div>
        </header>

        <main class="content">
            @yield('content')
        </main>
    </div>
</body>
</html>
