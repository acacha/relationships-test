<?php

namespace App\Console\Commands;

use Acacha\Relationships\Wrappers\CodigosPostalesListImport;
use App;
use File;
use Illuminate\Console\Command;
use Scool\EbreEscoolModel\Person;

/**
 * Class SeedIdentifiers.
 *
 * @package App\Console\Commands
 */
class SeedIdentifiers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seed:identifiers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed identifiers from Ebre-escool';

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
        seed_identifier_types();

        $persons = Person::all();

        foreach ($persons as $person) {
            if (! $person->official_id_type) $person->official_id_type = 1;
            if ( ! in_array($person->official_id_type, [1,2,3,4]) ) {
                $person->official_id_type = 1;
            }
            if ($person->official_id) {
                first_or_create_identifier($this->clean($person->official_id), $person->official_id_type );
            }

            if ( ! in_array($person->secondary_official_id_type, [1,2,3,4]) ) {
                $person->secondary_official_id_type = 1;
            }

            if ($person->secondary_official_id) {
                first_or_create_identifier($this->clean($person->secondary_official_id), $person->person_secondary_official_id_type );
            }
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
