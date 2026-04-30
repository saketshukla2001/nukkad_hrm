<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Candidate extends Model
{
    protected $fillable = [
        'name',
        'designation',
        'location_hq',
        'date_of_commencement',
        'reporting_boss',
        'ctc_annual',
        'ctc_in_word',
        'basic_pay',
        'hra',
        'annual_leave_days',
        'sick_leave_days',
        'monthly_salary',
        'target_percentage',
        'aadhar_file',
    ];
}
