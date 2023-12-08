<?php

namespace App\Console\Commands;

use Goutte\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ScrapData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:scrap-data';


    protected $description = 'This command will scrap data at midnight';

    /**
     * Execute the console command.
     */
    public function handle()
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
                    $designation = $this->designation->create([
                        'title' => $staffDetail['position']
                    ]);
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
}
