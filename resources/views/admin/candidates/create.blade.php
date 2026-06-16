@extends('admin.layout')

@section('content')
<div class="card">
    <div class="page-header">
        <div>
            <h2>Add Candidate</h2>
            <p class="subtitle">Create a new candidate profile for offer letter generation</p>
        </div>
        <a class="btn-secondary" href="{{ route('admin.candidates.index') }}">← Back to List</a>
    </div>

    @if(session('success'))
        <div class="alert">{{ session('success') }}</div>
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

    <form method="POST" action="{{ route('admin.candidates.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="form-grid">
            <div class="field">
                <label>Full Name</label>
                <input type="text" name="name" value="{{ old('name') }}" placeholder="Enter candidate name">
            </div>

            <div class="field">
                <label>Designation</label>
                <input type="text" name="designation" value="{{ old('designation') }}" placeholder="e.g. Sales Executive">
            </div>

            <div class="field">
                <label>Location (HQ)</label>
                <input type="text" name="location_hq" value="{{ old('location_hq') }}" placeholder="Enter HQ location">
            </div>

            <div class="field">
                <label>Date of Commencement</label>
                <input type="date" name="date_of_commencement" value="{{ old('date_of_commencement') }}">
            </div>

            <div class="field">
                <label>Reporting Boss</label>
                <input type="text" name="reporting_boss" value="{{ old('reporting_boss') }}" placeholder="Enter reporting manager">
            </div>

            <div class="field">
                <label>CTC (Annual)</label>
                <input type="number" step="0.01" name="ctc_annual" value="{{ old('ctc_annual') }}" placeholder="e.g. 600000">
            </div>

            <div class="field">
                <label>CTC in Words</label>
                <input type="text" name="ctc_in_word" value="{{ old('ctc_in_word') }}" placeholder="Six Lakhs Only">
            </div>

            <div class="field">
                <label>Basic Pay (Monthly)</label>
                <input type="number" step="0.01" name="basic_pay" value="{{ old('basic_pay') }}" placeholder="Auto: 50% of CTC">
            </div>

            <div class="field">
                <label>HRA (Monthly)</label>
                <input type="number" step="0.01" name="hra" value="{{ old('hra') }}" placeholder="Auto: 40% of Basic">
            </div>

            <div class="field">
                <label>Annual Leave (Days)</label>
                <input type="number" name="annual_leave_days" value="{{ old('annual_leave_days') }}" placeholder="e.g. 18">
            </div>

            <div class="field">
                <label>Sick Leave (Days)</label>
                <input type="number" name="sick_leave_days" value="{{ old('sick_leave_days') }}" placeholder="e.g. 12">
            </div>

            <div class="field">
                <label>Monthly Salary (Gross)</label>
                <input type="number" step="0.01" name="monthly_salary" value="{{ old('monthly_salary') }}" placeholder="Gross monthly salary">
            </div>

            <div class="field">
                <label>Target % for Incentives</label>
                <input type="number" name="target_percentage" value="{{ old('target_percentage') }}" placeholder="e.g. 80">
            </div>

            <div class="field span-2">
                <label>Aadhar Card</label>
                <input type="file" name="aadhar_file">
            </div>
        </div>

        <div style="margin-top: 24px; display:flex; gap:12px; align-items:center;">
            <button type="submit" class="btn-primary">💾 Save Candidate</button>
            <a class="btn-secondary" href="{{ route('admin.candidates.index') }}">Cancel</a>
        </div>
    </form>
</div>
@endsection
