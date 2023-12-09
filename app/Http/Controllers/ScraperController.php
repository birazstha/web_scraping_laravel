<?php

namespace App\Http\Controllers;

use App\Exports\StaffDataExport;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Staff;
use Carbon\Carbon;
use Goutte\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

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
        $data = [
            'departments' => $this->department->get(),
            'designations' => $this->designation->get(),
            'staffs' => $this->staff->get()
        ];
        return view('index', $data);
    }

    public function filterStaff(Request $request)
    {
        $designationId = $request->designation_id ??  request()->query('designation_id');
        $departmentId = $request->department_id ?? request()->query('department_id');
        $staffName = $request->name ??  request()->query('name');

        $staffQuery = $this->staff;

        $staffs = $staffQuery
            ->when($designationId, function ($query) use ($designationId) {
                return $query->where('designation_id', $designationId);
            })
            ->when($departmentId, function ($query) use ($departmentId) {
                return $query->where('department_id', $departmentId);
            })
            ->when($staffName, function ($query) use ($staffName) {
                return $query->where('name', 'ILIKE', '%' . $staffName . '%');
            })
            ->get();


        $data = [
            'staffs' => $staffs,
            'departments' => $this->department->get(),
            'designations' => $this->designation->get(),
            'departmentTitle' => $this->department->where('id', $departmentId)->value('title') ?? ''
        ];

        if ($request->isExport) {
            return $data['staffs'];
        }
        return view('result', $data);
    }

    public function export(Request $request)
    {
        $exportData = $this->filterStaff($request->merge(['isExport' => true]));
        if ($exportData->isNotEmpty()) {
            return Excel::download(new StaffDataExport($exportData), 'staff_data_' . Carbon::now() . '.xlsx');
        } else {
            return redirect()->back()->withErrors(['msg' => "Data can't be exported because there is no any data."]);
        }
    }
}
