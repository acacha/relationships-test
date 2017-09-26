<?php

namespace App\Console\Commands;

use Acacha\Relationships\Wrappers\CodigosPostalesListImport;
use App;
use File;
use Illuminate\Console\Command;
use Scool\EbreEscoolModel\Person;
use PragmaRX\Countries\Facade as Countries;

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
        $persons = Person::all();

        foreach ($persons as $person) {
            if(! trim($person->homePostalAddress)) continue;
//            $postalcode = $person->postalcode;

//            "person_locality_id" => 360
//    "person_locality_name" => "Tortosa"

            dump($person);
            dump($person->location);
            continue;
//            $country = "España";
            $fullname = trim($person->homePostalAddress);
            $name = null;
            //TODO default Type : Carrer
            $type = null;
            $number = null;
            $floor = null;
            $floor_number = null;
            $postalcode = null;

//            "person_locality_id" => 3
//    "person_locality_name" => ""
//            "postalcode" => ""
//    "state" => ""


            $location = null;
            $province = $this->obtainProvinceIdByProvinceName($person->state);
//            $state = $this->obtainStateIdByProvinceName($person->state);
            $country_code = "ESP";
            first_or_create_address(
                $fullname,
                $name,
                $type,
                $number,
                $floor,
                $floor_number,
                $postalcode,
                $location,
                $province,
                $country_code
            );

        }
    }

}
