<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Designation;
use App\Models\Staff;
use Goutte\Client;
use Illuminate\Support\Facades\DB;

class ScraperController extends Controller
{

    public $staff, $department, $designation;

    public function __construct(
        Staff $staff,
        Department $department,
        Designation $designation
    ) {
        $this->staff = $staff;
        $this->department = $department;
        $this->designation = $designation;
    }

    public function scrapper()
    {
        $staffs = $this->staff->get();
        return view('scrapper', compact('staffs'));
    }
}
