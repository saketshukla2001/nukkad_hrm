<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Candidate;

class CandidateController extends Controller
{
    public function index()
    {
        $candidates = Candidate::orderBy('id', 'desc')->get();
        return view('admin.candidates.index', compact('candidates'));
    }

    public function create()
    {
        return view('admin.candidates.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'designation' => 'required|string',
            'location_hq' => 'required|string',
            'date_of_commencement' => 'required|date',
            'reporting_boss' => 'required|string',
            'ctc_annual' => 'required|numeric',
            'ctc_in_word' => 'required|string',
            'basic_pay' => 'required|numeric',
            'hra' => 'required|numeric',
            'annual_leave_days' => 'required|integer',
            'sick_leave_days' => 'required|integer',
            'monthly_salary' => 'required|numeric',
            'target_percentage' => 'required|integer',
            'aadhar_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp',
        ]);

        $data = $request->all();

        if ($request->hasFile('aadhar_file')) {
            $file = $request->file('aadhar_file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $dir = public_path('uploads/aadhar');
            if (!is_dir($dir)) {
                @mkdir($dir, 0775, true);
            }
            $file->move($dir, $filename);
            $data['aadhar_file'] = $filename;
        }

        Candidate::create($data);

        return redirect()->route('admin.candidates.index')->with('success', 'Candidate added successfully!');
    }

    public function edit($id)
    {
        $candidate = Candidate::findOrFail($id);
        return view('admin.candidates.edit', compact('candidate'));
    }

    public function update(Request $request, $id)
    {
        $candidate = Candidate::findOrFail($id);

        $request->validate([
            'name' => 'required|string',
            'designation' => 'required|string',
            'location_hq' => 'required|string',
            'date_of_commencement' => 'required|date',
            'reporting_boss' => 'required|string',
            'ctc_annual' => 'required|numeric',
            'ctc_in_word' => 'required|string',
            'basic_pay' => 'required|numeric',
            'hra' => 'required|numeric',
            'annual_leave_days' => 'required|integer',
            'sick_leave_days' => 'required|integer',
            'monthly_salary' => 'required|numeric',
            'target_percentage' => 'required|integer',
            'aadhar_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp',
        ]);

        $data = $request->all();

        if ($request->hasFile('aadhar_file')) {
            $file = $request->file('aadhar_file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $dir = public_path('uploads/aadhar');
            if (!is_dir($dir)) {
                @mkdir($dir, 0775, true);
            }
            $file->move($dir, $filename);
            $data['aadhar_file'] = $filename;
        }

        $candidate->update($data);

        return redirect()->route('admin.candidates.index')->with('success', 'Candidate updated successfully!');
    }
}
