<?php

namespace App\Console\Commands;

use Acacha\Relationships\Wrappers\CodigosPostalesListImport;
use App;
use File;
use Illuminate\Console\Command;
use Scool\EbreEscoolModel\Person;

/**
 * Class SeedContacts.
 *
 * @package App\Console\Commands
 */
class SeedContacts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seed:contacts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed contacts from Ebre-escool';

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
        seed_contact_types();

        $persons = Person::all();

        foreach ($persons as $person) {
            if ($person->email) first_or_create_contact($this->clean($person->email), 'email');
            if ($person->secondary_email) first_or_create_contact($this->clean($person->secondary_email), 'email');
            if ($person->terciary_email) first_or_create_contact($this->clean($person->terciary_email), 'email');

            if ($person->telephoneNumber) first_or_create_contact($this->clean($person->telephoneNumber), 'telèfon');
            if ($person->mobile) first_or_create_contact($this->clean($person->mobile), 'telèfon');
        }

    }

    /**
     * @param $string
     * @return mixed
     */
    protected function clean($string)
    {
        return preg_replace('/\s+/', '', trim($string));
    }

}
