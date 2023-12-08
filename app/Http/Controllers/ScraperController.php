<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Designation;
use App\Models\Staff;
use Goutte\Client;
use Illuminate\Http\Request;
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
                // Scraping section title
                $sectionTitleNode = $staffSectionNode->filter('.s-title-section');
                $sectionTitle = $sectionTitleNode->count() > 0 ? $sectionTitleNode->text() : 'Unknown Section';

                // Scraping staff details within the section
                $staffDetails = $staffSectionNode->filter('.single-profile')->each(function ($staffNode) {
                    $name = $staffNode->filter('.name')->text();
                    $position = $staffNode->filter('.position')->text();
                    $image = $staffNode->filter('.profile-img img')->attr('src');

                    // Add the scraped data to the array
                    return [
                        'name' => $name,
                        'position' => $position,
                        'image' => $image,
                    ];
                });

                // Return an array containing section title and staff details
                return [
                    'sectionTitle' => $sectionTitle,
                    'staffDetails' => $staffDetails,
                ];
            });

            foreach ($staffSections as $staffSection) {
                $department = $this->department->create([
                    'title' => $staffSection['sectionTitle']
                ]);

                foreach ($staffSection['staffDetails'] as $staffDetail) {
                    $designationExists = $this->designation->where('title', $staffDetail['position'])->first();
                    if (!isset($designationExists)) {
                        $designation = $this->designation->create([
                            'title' => $staffDetail['position']
                        ]);
                    }
                    $this->staff->create([
                        'name' => $staffDetail['name'],
                        'designation_id' => $designation->id,
                        'department_id' => $department->id,
                        'image' => $staffDetail['image']
                    ]);
                }
            }
        });
    }

    public function filterStaff(Request $request)
    {
        $designationId = $request->designation_id;
        $departmentId = $request->department_id;
        $staffs = [];
        if ($designationId && $departmentId) {
            $staffs = $this->staff->where(['designation_id' => $designationId, 'department_id' => $departmentId])->get();
        } else if ($designationId) {
            $staffs = $this->staff->where('designation_id', $designationId)->get();
        } else {
            $staffs = $this->staff->where('department_id', $departmentId)->get();
        }

        $data = [
            'staffs' => $staffs,
            'departments' => $this->department->get(),
            'designations' => $this->designation->get(),
        ];
        return view('result', $data);
    }
}
