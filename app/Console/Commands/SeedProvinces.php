<?php

namespace App\Console\Commands;

use App;
use Illuminate\Console\Command;
use Scool\EbreEscoolModel\Person;
use PragmaRX\Countries\Facade as Countries;

/**
 * Class SeedProvinces.
 *
 * @package App\Console\Commands
 */
class SeedProvinces extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seed:provinces';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed provinces';

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
        //https://gist.github.com/daguilarm/ef9f91bf9c934bab3a70
        seed_states();
        seed_provinces();
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
