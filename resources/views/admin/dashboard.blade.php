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
            <div class="value">{{ number_format($totalCandidates) }}</div>
            <div class="hint">Total candidates in system</div>
        </div>
        <div class="stat-card">
            <div class="label">Offer Letter Templates</div>
            <div class="value">{{ number_format($totalOfferLetters) }}</div>
            <div class="hint">Templates available for generation</div>
        </div>
        <div class="stat-card">
            <div class="label">This Month</div>
            <div class="value">{{ number_format($candidatesThisMonth) }}</div>
            <div class="hint">New candidates added this month</div>
        </div>
    </div>

    <div class="card">
        <div class="page-header">
            <div>
                <h2 style="margin:0;">Recent Candidates</h2>
                <p class="subtitle">Latest candidate profiles added in the system.</p>
            </div>
            <div style="display:flex; gap:10px; flex-wrap:wrap;">
                <a class="btn-secondary" href="{{ route('admin.candidates.index') }}">View All</a>
                <a class="btn-secondary" href="{{ route('admin.offerletter.template.edit') }}">Edit Offer Template</a>
            </div>
        </div>

        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Designation</th>
                        <th>Location</th>
                        <th style="width:180px;">Added On</th>
                        <th style="width:170px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($latestCandidates as $candidate)
                        <tr>
                            <td style="font-weight:800; color:rgba(15,23,42,0.88);">{{ $candidate->name }}</td>
                            <td>{{ $candidate->designation }}</td>
                            <td>{{ $candidate->location_hq }}</td>
                            <td>{{ optional($candidate->created_at)->format('d M Y') }}</td>
                            <td>
                                <a class="btn-link" href="{{ route('admin.offerletter.generate', $candidate->id) }}">Generate Offer</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align:center; color:rgba(100,116,139,0.95); padding:18px;">
                                No candidates found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
