<?php

namespace App\Http\Controllers;

use App\Exports\StaffDataExport;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Staff;
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
        return view('scrapper', $data);
    }

    public function scrapData()
    {
        DB::transaction(function () {
            $client = new Client();

            $url = 'https://moics.gov.np/en/staff';
            $crawler = $client->request('GET', $url);

            $staffSections = $crawler->filter('.tab-pane.fade.sub-area')->each(function ($staffSectionNode) {
                // Scraping Departments
                $sectionTitleNode = $staffSectionNode->filter('.s-title-section');
                $sectionTitle = $sectionTitleNode->count() > 0 ? $sectionTitleNode->text() : 'Unknown Section';

                // Scraping Staff's Name, Position, and Image.
                $staffDetails = $staffSectionNode->filter('.single-profile')->each(function ($staffNode) {
                    $name = $staffNode->filter('.name')->text();
                    $position = $staffNode->filter('.position')->text();
                    $image = $staffNode->filter('.profile-img img')->attr('src');

                    return [
                        'name' => $name,
                        'position' => $position,
                        'image' => $image,
                    ];
                });

                return [
                    'sectionTitle' => $sectionTitle,
                    'staffDetails' => $staffDetails,
                ];
            });

            foreach ($staffSections as $staffSection) {
                if ($staffSection['staffDetails']) {
                    $department = $this->department->firstOrCreate([
                        'title' => $staffSection['sectionTitle']
                    ]);

                    foreach ($staffSection['staffDetails'] as $staffDetail) {
                        $designation = $this->designation->firstOrCreate([
                            'title' => $staffDetail['position']
                        ]);

                        $staffExists = $this->staff->where([
                            'name' => $staffDetail['name'],
                            'designation_id' => $designation->id,
                            'department_id' => $department->id
                        ])->first();

                        if (!$staffExists) {
                            $this->staff->create([
                                'name' => $staffDetail['name'],
                                'designation_id' => $designation->id,
                                'department_id' => $department->id,
                                'image' => $staffDetail['image']
                            ]);
                        }
                    }
                }
            }
        });
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
        return Excel::download(new StaffDataExport($exportData), 'staff_data.xlsx');
    }
}
