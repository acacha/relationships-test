<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Scool\EbreEscoolModel\Person;

/**
 * Class SeedLocations.
 *
 * @package App\Console\Commands
 */
class SeedLocations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seed:locations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed locations and postal codes from Ebre-escool';

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
        $locationsDone = [];
        foreach ($persons as $person) {
            if ($person->postalcode == null && $person->locality_name == null) continue;
            $key = trim($person->postalcode) . '_' . trim(ucfirst($person->locality_name));
            if ( ! array_key_exists($key,$locationsDone) ) {
                print("seed_location('" . trim($person->postalcode) . "', '" . trim(ucfirst($person->locality_name)) . "');\n");
            }
            $locationsDone[$key] = true ;
        }

    }
}
