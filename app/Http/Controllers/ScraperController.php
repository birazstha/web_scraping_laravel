<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Staff;
use Goutte\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ScraperController extends Controller
{

    public $staff, $department;

    public function __construct(Staff $staff, Department $department)
    {
        $this->staff = $staff;
        $this->department = $department;
    }



    public function scrapper()
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
                    $this->staff->create([
                        'name' => $staffDetail['name'],
                        'designation' => $staffDetail['position'],
                        'image' => $staffDetail['image'],
                        'department_id' => $department->id
                    ]);
                }
            }
        });
    }
}
