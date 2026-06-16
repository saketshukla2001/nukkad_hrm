@extends('admin.layout')

@section('content')
    <div class="welcome-banner">
        <h1>Welcome back, Admin 👋</h1>
        <p>Manage candidates, generate offer letters, and track your HR operations — all in one place.</p>
        <div class="banner-actions">
            <a class="btn-white" href="{{ route('admin.candidates.create') }}">➕ Add New Candidate</a>
            <a class="btn-ghost-white" href="{{ route('admin.offerletter.template.edit') }}">📝 Edit Offer Template</a>
        </div>
    </div>

    <div class="stat-grid">
        <div class="stat-card indigo">
            <div class="stat-card-header">
                <div class="stat-icon">👥</div>
            </div>
            <div class="label">Total Candidates</div>
            <div class="value">{{ number_format($totalCandidates) }}</div>
            <div class="hint">All candidate profiles in system</div>
        </div>
        <div class="stat-card green">
            <div class="stat-card-header">
                <div class="stat-icon">📄</div>
            </div>
            <div class="label">Offer Templates</div>
            <div class="value">{{ number_format($totalOfferLetters) }}</div>
            <div class="hint">Ready-to-use offer letter templates</div>
        </div>
        <div class="stat-card amber">
            <div class="stat-card-header">
                <div class="stat-icon">📅</div>
                @if($candidatesThisMonth > 0)
                    <span class="stat-trend up">+{{ $candidatesThisMonth }} new</span>
                @endif
            </div>
            <div class="label">This Month</div>
            <div class="value">{{ number_format($candidatesThisMonth) }}</div>
            <div class="hint">Candidates added this month</div>
        </div>
    </div>

    <div class="card">
        <div class="page-header">
            <div>
                <h2>Recent Candidates</h2>
                <p class="subtitle">Latest profiles added to the system</p>
            </div>
            <div class="page-header-actions">
                <a class="btn-secondary" href="{{ route('admin.candidates.index') }}">View All</a>
                <a class="btn-primary" href="{{ route('admin.candidates.create') }}">➕ Add Candidate</a>
            </div>
        </div>

        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Designation</th>
                        <th>Location</th>
                        <th style="width:160px;">Added On</th>
                        <th style="width:160px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($latestCandidates as $candidate)
                        <tr>
                            <td>
                                <div class="table-name">
                                    <div class="table-avatar">{{ strtoupper(substr($candidate->name, 0, 1)) }}</div>
                                    {{ $candidate->name }}
                                </div>
                            </td>
                            <td><span class="badge badge-indigo">{{ $candidate->designation }}</span></td>
                            <td>{{ $candidate->location_hq }}</td>
                            <td>{{ optional($candidate->created_at)->format('d M Y') }}</td>
                            <td>
                                <a class="btn-link" href="{{ route('admin.offerletter.generate', $candidate->id) }}">📝 Generate Offer</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="empty-state">
                                    <div class="empty-icon">👤</div>
                                    <p>No candidates found yet. Add your first candidate to get started.</p>
                                    <a class="btn-primary" href="{{ route('admin.candidates.create') }}">➕ Add Candidate</a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
