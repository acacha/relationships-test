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
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $persons = Person::all();

        $all = Countries::all();

        dd($all);

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
