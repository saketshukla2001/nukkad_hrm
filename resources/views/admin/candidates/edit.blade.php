@extends('admin.layout')

@section('content')
<div class="card">
    <div class="page-header">
        <div>
            <h2>Edit Candidate</h2>
            <p class="subtitle">Update details for <strong>{{ $candidate->name }}</strong></p>
        </div>
        <a class="btn-secondary" href="{{ route('admin.candidates.index') }}">← Back to List</a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul style="margin:0; padding-left:20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.candidates.update', $candidate->id) }}" enctype="multipart/form-data">
        @csrf

        <div class="form-grid">
            <div class="field">
                <label>Full Name</label>
                <input type="text" name="name" value="{{ old('name', $candidate->name) }}">
            </div>

            <div class="field">
                <label>Designation</label>
                <input type="text" name="designation" value="{{ old('designation', $candidate->designation) }}">
            </div>

            <div class="field">
                <label>Location (HQ)</label>
                <input type="text" name="location_hq" value="{{ old('location_hq', $candidate->location_hq) }}">
            </div>

            <div class="field">
                <label>Date of Commencement</label>
                <input type="date" name="date_of_commencement" value="{{ old('date_of_commencement', $candidate->date_of_commencement) }}">
            </div>

            <div class="field">
                <label>Reporting Boss</label>
                <input type="text" name="reporting_boss" value="{{ old('reporting_boss', $candidate->reporting_boss) }}">
            </div>

            <div class="field">
                <label>CTC (Annual)</label>
                <input type="number" step="0.01" name="ctc_annual" value="{{ old('ctc_annual', $candidate->ctc_annual) }}">
            </div>

            <div class="field">
                <label>CTC in Words</label>
                <input type="text" name="ctc_in_word" value="{{ old('ctc_in_word', $candidate->ctc_in_word) }}">
            </div>

            <div class="field">
                <label>Basic Pay (Monthly)</label>
                <input type="number" step="0.01" name="basic_pay" value="{{ old('basic_pay', $candidate->basic_pay) }}">
            </div>

            <div class="field">
                <label>HRA (Monthly)</label>
                <input type="number" step="0.01" name="hra" value="{{ old('hra', $candidate->hra) }}">
            </div>

            <div class="field">
                <label>Annual Leave (Days)</label>
                <input type="number" name="annual_leave_days" value="{{ old('annual_leave_days', $candidate->annual_leave_days) }}">
            </div>

            <div class="field">
                <label>Sick Leave (Days)</label>
                <input type="number" name="sick_leave_days" value="{{ old('sick_leave_days', $candidate->sick_leave_days) }}">
            </div>

            <div class="field">
                <label>Monthly Salary (Gross)</label>
                <input type="number" step="0.01" name="monthly_salary" value="{{ old('monthly_salary', $candidate->monthly_salary) }}">
            </div>

            <div class="field">
                <label>Target % for Incentives</label>
                <input type="number" name="target_percentage" value="{{ old('target_percentage', $candidate->target_percentage) }}">
            </div>

            <div class="field span-2">
                <label>Aadhar Card</label>
                <input type="file" name="aadhar_file">
                @if($candidate->aadhar_file)
                    <p style="font-size:12px; color:var(--muted); margin:4px 0 0;">Current file: <strong>{{ $candidate->aadhar_file }}</strong></p>
                @endif
            </div>
        </div>

        <div style="margin-top: 24px; display:flex; gap:12px; align-items:center; flex-wrap:wrap;">
            <button type="submit" class="btn-primary">✅ Update Candidate</button>
            <a class="btn-link" href="{{ route('admin.offerletter.generate', $candidate->id) }}">📝 Generate Offer Letter</a>
            <a class="btn-secondary" href="{{ route('admin.candidates.index') }}">Cancel</a>
        </div>
    </form>
</div>
@endsection
