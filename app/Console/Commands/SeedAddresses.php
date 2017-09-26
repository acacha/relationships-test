<?php

namespace App\Console\Commands;

use Acacha\Relationships\Models\Location;
use App;
use Illuminate\Console\Command;
use Scool\EbreEscoolModel\Person;

/**
 * Class SeedAddresses.
 *
 * @package App\Console\Commands
 */
class SeedAddresses extends Command
{
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
     * Obtain state id by province name.
     *
     * @param $name
     * @return int|null
     */
    protected function obtainStateIdByProvinceName($name)
    {
        if (! $name) return null;

        switch ($name) {
            case 'Tarragona':
                return 9;
            case 'Castelló':
                return 10;
            case 'Castellò':
                return 10;
            case 'Teruel':
                return 2;
            case 'València':
                return 10;
            case 'Velència':
                return 10;
            case 'Velencia':
                return 10;
            case 'Valencia':
                return 10;
            case 'Jaén':
                return 1;
            case 'Alicante':
                return 10;
            case 'Alacant':
                return 10;
            case 'Alicant':
                return 10;
            case 'Zaragoza':
                return 2;
            case 'Girona':
                return 9;
            case 'Menorca':
                return 4;
            case 'Barcelona':
                return 9;
            default:
                die('Error with: ' . $name);
        }

    }

    /**
     * Obtain province id by province name.
     *
     * @param $name
     * @return int|null
     */
    protected function obtainProvinceIdByProvinceName($name)
    {
        if (! $name) return null;

        switch ($name) {
            case 'Tarragona':
                return 36;
            case 'Castelló':
                return 51;
            case 'Castellò':
                return 51;
            case 'Teruel':
                return 7;
            case 'València':
                return 52;
            case 'Velència':
                return 52;
            case 'Valencia':
                return 52;
            case 'Jaén':
                return 19;
            case 'Alicante':
                return 50;
            case 'Alacant':
                return 50;
            case 'Alicant':
                return 50;
            case 'Zaragoza':
                return 8;
            case 'Girona':
                return 34;
            case 'Menorca':
                return 44;
            case 'Barcelona':
                return 33;
            default:
                die('Error with: ' . $name);
        }

    }

    /**
     * Execute the console command.
     *
     * @return mixed
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

    /**
     * Obtain location id by personal info.
     *
     * @param $person
     * @return int|null
     */
    protected function obtainLocationIdByPersonalInfo($person)
    {
        if ( $person->person_locality_name == null && $person->postalcode ==null ) return null;
        if ( $person->person_locality_name == "" && $person->postalcode == "" ) return null;

        $locations  = Location::where('name','like',$person->person_locality_name)->get();
        if ($locations->count() != 1) {
            if (!$person->postalcode) return null;
            $locations = $locations->where('postalcode', $person->postalcode);
        }
        if ($locations->count() == 0) {
            if (!$person->postalcode) return null;
            $locations = Location::where('postalcode', $person->postalcode);
        }
        if( $locations->count() == 0 ) return null;
        return $locations->first()->id;
    }

}
