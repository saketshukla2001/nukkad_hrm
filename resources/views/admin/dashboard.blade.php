@extends('admin.layout')

@section('content')
    <div class="card" style="margin-bottom:14px;">
        <div class="page-header" style="margin-bottom:0;">
            <div>
                <h1 style="margin:0;">Dashboard</h1>
                <p class="subtitle">Quick overview and shortcuts for daily admin work.</p>
            </div>
            <a class="btn-secondary" href="{{ route('admin.candidates.create') }}">➕ Add Candidate</a>
        </div>
    </div>

    <div class="stat-grid" style="margin-bottom:14px;">
        <div class="stat-card">
            <div class="label">Candidates</div>
            <div class="value">—</div>
            <div class="hint">Total candidates in system</div>
        </div>
        <div class="stat-card">
            <div class="label">Offer Letters</div>
            <div class="value">—</div>
            <div class="hint">Generated this month</div>
        </div>
        <div class="stat-card">
            <div class="label">Actions</div>
            <div class="value">Quick</div>
            <div class="hint">
                <a class="btn-link" href="{{ route('admin.candidates.index') }}">View candidates</a>
                <span style="color:rgba(100,116,139,0.7); padding:0 8px;">•</span>
                <a class="btn-link" href="{{ route('admin.offerletter.template.edit') }}">Edit offer template</a>
            </div>
        </div>
    </div>

    <div class="card">
        <h2 style="margin:0 0 10px;">Tips</h2>
        <p style="margin:0;">Use the left menu to manage candidates and quickly generate offer letters.</p>
    </div>
@endsection
