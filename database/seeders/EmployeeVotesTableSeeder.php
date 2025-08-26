<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EmployeeVote;
use Faker\Factory as Faker;
use Carbon\Carbon;

class EmployeeVotesTableSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        // Define possible values for each field
        $bases = ['ahmed xani', 'jaznika', 'goran'];
        $unitOffices = [
            'HR Department',
            'Finance',
            'IT',
            'Operations',
            'Marketing',
            'Sales',
            'Customer Support'
        ];

        // Kurdish names for realistic data
        $kurdishNames = [
            'Aram Ahmed',
            'Dlshad Mohammed',
            'Shvan Omar',
            'Haval Ibrahim',
            'Rebin Hassan',
            'Dana Abdullah',
            'Shilan Karim',
            'Rozhgar Ali',
            'Baran Othman',
            'Diyar Rasul',
            'Hemin Salih',
            'Lana Kamal',
            'Avin Mahmoud',
            'Soran Najat',
            'Vian Azad',
            'Dilshad Farhad'
        ];

        $circleList = [
            '1',
            '2',
            '3'
        ];
        $baseList = [
            'slemani-01',
            'slemani-02',
            'slemani-03',
            'slemani-04',
            'slemani-05',

            'hawler-01',
            'hawler-02',
            'hawler-03',
            'hawler-04',
            'hawler-05',

            'karkuk-01',
            'karkuk-02',
            'karkuk-03',
            'karkuk-04',
            'karkuk-05',

        ];

        for ($i = 0; $i < 30000; $i++) {
            $isElection = $faker->boolean(70); // 70% chance of having voted

            $employee = [
                'fullName' => $faker->randomElement($kurdishNames),
                'mobile' => '0750' . $faker->numerify('#######'),
                'address' => $faker->city . ', ' . $faker->streetAddress,
                'card_number' => $faker->unique()->numerify('EMP#######'),
                'unit_office' => $faker->randomElement($unitOffices),
                'base' => $faker->randomElement($baseList),
                // 'base_id' => $faker->randomElement($baseList),
                // 'circle_id' => $faker->randomElement($circleList),
                'is_election' => $isElection,
                'note' => $isElection ? null : $faker->randomElement([
                    'Not available',
                    'On leave',
                    'Sick',
                    'Business trip',
                    null,
                    null
                ]),
                'datetime' => $isElection ? Carbon::now()->subDays(rand(0, 30)) : null,
                'user_id' => $isElection ? rand(1, 10) : null,
                'created_at' => Carbon::now()->subDays(rand(0, 60)),
                'updated_at' => Carbon::now()->subDays(rand(0, 60)),
            ];

            EmployeeVote::create($employee);
        }
    }
}
