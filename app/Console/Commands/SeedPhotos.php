<?php

namespace App\Console\Commands;

use File;
use Illuminate\Console\Command;
use Scool\EbreEscoolModel\Person;
use SSH;

/**
 * Class SeedPhotos.
 *
 * @package App\Console\Commands
 */
class SeedPhotos extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seed:photos {--skip-download : Do not download files}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed photos from Ebre-escool';

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
     */
    public function handle()
    {
        $persons = Person::all();

        foreach ($persons as $person) {
            if ($person->person_photo) {
                dump($person->person_photo);
                $remotePath = '/usr/share/ebre-escool/uploads/person_photos/' . $person->person_photo;
//                /usr/share/ebre-escool/uploads/person_photos/201011olgasotelo.jpg
                $local_storage = '/../relationships-test/storage/';
                $folder = 'photos/';
                $localPath = RELATIONSHIPS_PATH . $local_storage . $folder . $person->person_photo;
                first_or_create_photo( 'local_photos' , $folder . $person->person_photo);

                if (! $this->option('skip-download')) SSH::into('ebre-escool')->get($remotePath, $localPath);
                
            }
        }
    }
}
