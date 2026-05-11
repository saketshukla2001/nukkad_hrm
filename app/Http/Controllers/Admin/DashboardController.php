<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\OfferLetter;

class DashboardController extends Controller
{
    public function index()
    {
        $totalCandidates = Candidate::count();
        $totalOfferLetters = OfferLetter::count();
        $candidatesThisMonth = Candidate::where('created_at', '>=', now()->startOfMonth())->count();
        $latestCandidates = Candidate::latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'totalCandidates',
            'totalOfferLetters',
            'candidatesThisMonth',
            'latestCandidates'
        ));
    }
}
