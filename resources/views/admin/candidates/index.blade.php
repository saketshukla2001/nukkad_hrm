@extends('admin.layout')

@section('content')
<div class="card">
    <div class="page-header">
        <div>
            <h2>Candidates</h2>
            <p class="subtitle">Manage candidate profiles and generate offer letters</p>
        </div>
        <a href="{{ route('admin.candidates.create') }}" class="btn-primary">➕ Add Candidate</a>
    </div>

    @if(session('success'))
        <div class="alert">{{ session('success') }}</div>
    @endif

    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th style="width:60px;">#</th>
                    <th>Name</th>
                    <th>Designation</th>
                    <th>Location</th>
                    <th style="width:150px;">Monthly Salary</th>
                    <th style="width:240px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($candidates as $index => $candidate)
                    <tr>
                        <td><span class="badge badge-slate">{{ $index + 1 }}</span></td>
                        <td>
                            <div class="table-name">
                                <div class="table-avatar">{{ strtoupper(substr($candidate->name, 0, 1)) }}</div>
                                {{ $candidate->name }}
                            </div>
                        </td>
                        <td><span class="badge badge-indigo">{{ $candidate->designation }}</span></td>
                        <td>{{ $candidate->location_hq }}</td>
                        <td>
                            @if($candidate->monthly_salary)
                                <span class="badge badge-green">₹{{ number_format($candidate->monthly_salary) }}</span>
                            @else
                                <span class="badge badge-slate">—</span>
                            @endif
                        </td>
                        <td>
                            <div class="actions">
                                <a class="btn-secondary btn-sm" href="{{ route('admin.candidates.edit', $candidate->id) }}">✏️ Edit</a>
                                <a class="btn-link" href="{{ route('admin.offerletter.generate', $candidate->id) }}">📝 Offer Letter</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <div class="empty-icon">📋</div>
                                <p>No candidates found. Start by adding a new candidate profile.</p>
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
