@extends('admin.layout')

@section('content')
<div class="card">
    <div class="page-header">
        <div>
            <h2 style="margin:0;">Add Candidate</h2>
            <p class="subtitle">Create a new candidate profile.</p>
        </div>
        <a class="btn-secondary" href="{{ route('admin.candidates.index') }}">← Back</a>
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

    <form method="POST" action="{{ route('admin.candidates.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="form-grid">

            <div class="field">
                <label>Name</label>
                <input type="text" name="name" value="{{ old('name') }}" placeholder="Enter name">
            </div>

            <div class="field">
                <label>Designation</label>
                <input type="text" name="designation" value="{{ old('designation') }}" placeholder="Enter designation">
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
                <input type="text" name="reporting_boss" value="{{ old('reporting_boss') }}" placeholder="Enter reporting boss">
            </div>

            <div class="field">
                <label>CTC (Annual)</label>
                <input type="number" step="0.01" name="ctc_annual" value="{{ old('ctc_annual') }}" placeholder="Enter annual CTC">
            </div>

            <div class="field">
                <label>CTC in Word (Annual)</label>
                <input type="text" name="ctc_in_word" value="{{ old('ctc_in_word') }}" placeholder="Enter CTC in words">
            </div>

            <div class="field">
                <label>Basic Pay</label>
                <input type="number" step="0.01" name="basic_pay" value="{{ old('basic_pay') }}" placeholder="Enter basic pay">
            </div>

            <div class="field">
                <label>HRA</label>
                <input type="number" step="0.01" name="hra" value="{{ old('hra') }}" placeholder="Enter HRA">
            </div>

            <div class="field">
                <label>Annual Leave (Days)</label>
                <input type="number" name="annual_leave_days" value="{{ old('annual_leave_days') }}" placeholder="Enter annual leave days">
            </div>

            <div class="field">
                <label>Sick Leave (Days)</label>
                <input type="number" name="sick_leave_days" value="{{ old('sick_leave_days') }}" placeholder="Enter sick leave days">
            </div>

            <div class="field">
                <label>Monthly Salary</label>
                <input type="number" step="0.01" name="monthly_salary" value="{{ old('monthly_salary') }}" placeholder="Enter monthly salary">
            </div>

            <div class="field">
                <label>Target Percentage for Incentives</label>
                <input type="number" name="target_percentage" value="{{ old('target_percentage') }}" placeholder="Enter target percentage">
            </div>

            <div class="field span-2">
                <label>Aadhar Card</label>
                <input type="file" name="aadhar_file">
            </div>

        </div>

        <div style="margin-top: 18px; display:flex; gap:10px; align-items:center;">
            <button type="submit" class="btn-primary">💾 Save Candidate</button>
            <a class="btn-secondary" href="{{ route('admin.candidates.index') }}">Cancel</a>
        </div>
    </form>
</div>
@endsection
