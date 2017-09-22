<?php

namespace App\Console\Commands;

use Acacha\Relationships\Wrappers\CodigosPostalesListImport;
use App;
use File;
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
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $persons = Person::all();

        foreach ($persons as $person) {
            dd($person);
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
