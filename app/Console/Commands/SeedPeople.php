<?php

namespace App\Console\Commands;

use Acacha\Relationships\Models\Location;
use App;
use Illuminate\Console\Command;
use Scool\EbreEscoolModel\Person;

/**
 * Class SeedPeople.
 *
 * @package App\Console\Commands
 */
class SeedPeople extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seed:people';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed people from Ebre-escool';

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
     * Calculate surname.
     *
     * @param $sn1
     * @param $sn2
     * @return null|string
     */
    public function calculateSurname($sn1, $sn2)
    {
        if ($sn1 == null || $sn1 == "") return null;
        if ($sn2 == null || $sn2 == "") return $sn1;
        return $sn1 . ' ' . $sn2;
    }

    /**
     * Calculate name.
     *
     * @param $gn
     * @param $sn1
     * @param $sn2
     * @return null|string
     */
    public function calculateName($gn, $sn1, $sn2)
    {
        if ($gn == null || $gn == "") return null;
        if ($sn1 == null || $sn1 == "") return $gn;
        if ($sn2 == null || $sn2 == "") return $gn . ' ' . $sn1;
        return $gn .' ' . $sn1 . ' ' . $sn2;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //Dependencies
        seed_provinces();

        $persons = Person::all();

        foreach ($persons as $person) {
            dump($person);
//            continue;
            $name = $this->calculateName($person->person_givenName, $person->person_sn1, $person->person_sn2);
            $givenName = $person->person_givenName;
            $surname = $this->calculateSurname($person->person_sn1, $person->person_sn2);
            $surname1 = $person->person_sn1;
            $surname2 = $person->person_sn2;
            $birthdate = $this->calculateBrithDate($person->date_of_birth);
            $birthplace_id = null;
            $gender = $this->calculateGender($person->person_gender);
            $civil_status = null;
            first_or_create_people(
                $name,
                $givenName,
                $surname,
                $surname1,
                $surname2,
                $birthdate,
                $birthplace_id,
                $gender,
                $civil_status
            );

        }
    }

    /**
     * Calculate date of birth.
     *
     * @param $date
     * @return null
     */
    protected function calculateBrithDate($date)
    {
        if ( $date == "0000-00-00" ) return null;
        if ( ! $date) return null;
        return $date;
    }

    /**
     * Calculate gender.
     *
     * @param $person_gender
     * @return null|string
     */
    protected function calculateGender($person_gender)
    {
        if ( ! $person_gender) return null;
        switch ($person_gender) {
            case 'M':
                return 'Male';
            case 'F':
                return 'Female';
            default:
                die('Unvalid gender value: ' . $person_gender);
        }
    }


}
