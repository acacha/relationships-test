<?php

namespace App\Console\Commands;

use App;
use Illuminate\Console\Command;
use Scool\EbreEscoolModel\Person;
use PragmaRX\Countries\Facade as Countries;

/**
 * Class SeedCountries.
 *
 * @package App\Console\Commands
 */
class SeedCountries extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seed:countries';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed countries from Ebre-escool';

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
     * @return mixed
     */
    public function handle()
    {
//        dd('CACA');
        $persons = Person::all();

        dd(Countries::where('cca3', 'ESP')->first());


//        $countries = Countries::all();
        $countries = Countries::all()->pluck('name.translations.spa');

        foreach ($countries as $country) {
            dump($country);
        }



        foreach ($persons as $person) {
            dd($person->postalcode);
//            $province = obtainProvinceByPostalCode($person->postalcode);
//            $country = "España";
//            first_or_create_address(
//                $postalcode,
//                $location_id,
//                $province,
//                $country
//            );
//            "person_homePostalAddress" => "Alcanyiz 26 Atic 2"
//            $table->string('name');
//            $table->string('fullname');
//            $table->string('type');
//            $table->string('number');
//            $table->string('floor');
//            $table->string('floor_number');
//            $table->integer('postalcode')->unsigned();
//            $table->integer('location')->unsigned();
//            $table->string('province');
//            $table->string('country');
        }
    }

    /**
     * Clean string.
     *
     * @param $string
     * @return mixed
     */
    protected function clean($string)
    {
        return preg_replace('/\s+/', '', trim($string));
    }
}
