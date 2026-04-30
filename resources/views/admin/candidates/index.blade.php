@extends('admin.layout')

@section('content')
<div class="card">
    <div class="page-header">
        <div>
            <h2 style="margin:0;">Candidates</h2>
            <p class="subtitle">Manage candidate profiles and generate offer letters.</p>
        </div>
        <a href="{{ route('admin.candidates.create') }}" class="btn-primary">➕ Add Candidate</a>
    </div>

    @if(session('success'))
        <div class="alert">
            {{ session('success') }}
        </div>
    @endif

    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th style="width:70px;">#</th>
                    <th>Name</th>
                    <th>Designation</th>
                    <th>Location</th>
                    <th style="width:140px;">Monthly Salary</th>
                    <th style="width:220px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($candidates as $index => $candidate)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td style="font-weight:800; color:rgba(15,23,42,0.88);">{{ $candidate->name }}</td>
                        <td>{{ $candidate->designation }}</td>
                        <td>{{ $candidate->location_hq }}</td>
                        <td>{{ $candidate->monthly_salary }}</td>
                        <td>
                            <div class="actions">
                                <a class="btn-secondary" href="{{ route('admin.candidates.edit', $candidate->id) }}">✏️ Edit</a>
                                <a class="btn-link" href="{{ route('admin.offerletter.generate', $candidate->id) }}">📝 Generate Offer</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align:center; color:rgba(100,116,139,0.95); padding:18px;">
                            No candidates found
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
