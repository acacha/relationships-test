<?php

namespace App\Console\Commands;

use Acacha\Relationships\Wrappers\CodigosPostalesListImport;
use App;
use File;
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
        $excel = App::make('excel');
        $sourceFiles = RELATIONSHIPS_PATH . '/data/postalcodes';
        $files = $this->sort(File::allFiles($sourceFiles));
        foreach ($files as $file)
        {
//            dump((string) $file);
            $codigosPostales = new CodigosPostalesListImport(app(),$excel, (string) $file);
            $codigosPostales->noHeading();
            $codigosPostales->each(function($codigoPostal) {
                $postalcode = "0";
                $location = "1";
                first_or_create_location( trim($codigoPostal->$location) ,trim($codigoPostal->$postalcode) );
            });
        }
    }

    /**
     * @param $files
     * @return array
     */
    private function sort($files)
    {
        $files = array_sort($files, function ($file) {
            return $file->getFilename();
        });
        return $files;
    }
}
