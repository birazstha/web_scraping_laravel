<?php

namespace App\Console\Commands;

use App\Http\Controllers\ScraperController;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Staff;
use Goutte\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ScrapData extends Command
{

    protected $signature = 'app:scrap-data';
    protected $description = 'This command will scrap data at midnight';

    public $department, $designation, $staff;

    public function __construct(Department $department, Designation $designation, Staff $staff)
    {
        parent::__construct();
        $this->department = $department;
        $this->designation = $designation;
        $this->staff = $staff;
    }



    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            DB::transaction(function () {
                $client = new Client();
                $url = 'https://moics.gov.np/en/staff';
                $crawler = $client->request('GET', $url);

                $staffs = $crawler->filter('.tab-pane.fade.sub-area')->each(function ($staffInfo) {
                    // Scraping Departments
                    $sectionTitleDiv = $staffInfo->filter('.s-title-section');
                    $sectionTitle = $sectionTitleDiv->count() > 0 ? $sectionTitleDiv->text() : null;

                    // Scraping Staff's Name, Position,Image, Email, and Phone.
                    $staffDetails = $staffInfo->filter('.single-profile')->each(function ($staffDiv) {

                        $sectionNameDiv =  $staffDiv->filter('.name');
                        $sectionPositionDiv =  $staffDiv->filter('.position');
                        $sectionImageDiv =  $staffDiv->filter('.profile-img img');
                        $sectionEmailDiv =  $staffDiv->filter('.email-s');
                        $sectionPhoneDiv =  $staffDiv->filter('a.position');

                        $name = $sectionNameDiv->count() > 0 ? $sectionNameDiv->text() : null;
                        $position = $sectionPositionDiv->count() > 0 ? $sectionPositionDiv->text() : null;
                        $image = $sectionImageDiv->count() > 0 ? $sectionImageDiv->attr('src') : null;
                        $email = $sectionEmailDiv->count() > 0 ? $sectionEmailDiv->text() : null;
                        $phone = $sectionPhoneDiv->count() > 0 ? $sectionPhoneDiv->text() : null;

                        return [
                            'name' => $name,
                            'position' => $position,
                            'image' => $image,
                            'email' => $email,
                            'phone' => $phone,

                        ];
                    });

                    return [
                        'department' => $sectionTitle,
                        'staffDetails' => $staffDetails,
                    ];
                });


                foreach ($staffs as $staff) {
                    if ($staff['staffDetails']) {
                        //Check if the entered department exists in Department table.
                        //Only store new Department info.
                        $department = $this->department->firstOrCreate([
                            'title' => $staff['department']
                        ]);

                        foreach ($staff['staffDetails'] as $staffDetail) {
                            //Only Store Designation, if it is not in Designation table.
                            $designation = $this->designation->firstOrCreate([
                                'title' => $staffDetail['position']
                            ]);

                            //Check if there is staff with same designation and department. Only record the new ones.
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
                                    'image' => $staffDetail['image'],
                                    'email' => $staffDetail['email'],
                                    'phone' => $staffDetail['phone']
                                ]);
                            }
                        }
                    }
                }
            });
        } catch (RequestException $e) {
            Log::error('Error accessing the website: ' . $e->getMessage());
        }
    }
}
