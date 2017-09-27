<?php

namespace App\Console\Commands;

use App;
use App\Console\Commands\Traits\ObtainsLocationIdsByPersonalInfo;
use App\Console\Commands\Traits\ObtainsProvincesIdsByProvinceName;
use App\Console\Commands\Traits\ObtainStatesIdsByProvinceName;
use Illuminate\Console\Command;
use Scool\EbreEscoolModel\Person;

/**
 * Class SeedAddresses.
 *
 * @package App\Console\Commands
 */
class SeedAddresses extends Command
{
    use ObtainStatesIdsByProvinceName,
        ObtainsProvincesIdsByProvinceName,
        ObtainsLocationIdsByPersonalInfo;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seed:addresses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed addresses from Ebre-escool';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     */
    public function handle()
    {
        seed_provinces();
        $persons = Person::all();

        foreach ($persons as $person) {
            if(! trim($person->homePostalAddress)) continue;

            $fullname = trim($person->homePostalAddress);
            $name = null;
            $type = null;
            $number = null;
            $floor = null;
            $floor_number = null;
            $location = $this->obtainLocationIdByPersonalInfo($person);
            $province = $this->obtainProvinceIdByProvinceName($person->state);
            $country_code = "ESP";
            first_or_create_address(
                $fullname,
                $name,
                $type,
                $number,
                $floor,
                $floor_number,
                $location,
                $province,
                $country_code
            );

        }
    }
}
