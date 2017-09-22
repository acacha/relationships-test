<?php

namespace App\Console\Commands;

use App;
use Illuminate\Console\Command;
use Scool\EbreEscoolModel\Person;
use PragmaRX\Countries\Facade as Countries;

/**
 * Class SeedStates.
 *
 * @package App\Console\Commands
 */
class SeedStates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seed:states';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed states';

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
        //https://gist.github.com/daguilarm/0e93b73779f0306e5df2
        seed_states();
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
