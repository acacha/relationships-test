<?php

namespace App\Console\Commands;

use Acacha\Relationships\Models\Address;
use Acacha\Relationships\Models\Contact;
use Acacha\Relationships\Models\Identifier;
use Acacha\Relationships\Models\PersonMigrationInfo;
use Acacha\Relationships\Models\Photo;
use Acacha\Relationships\Models\UserMigrationInfo;
use App;
use App\Console\Commands\Traits\ObtainsLocationIdsByPersonalInfo;
use App\Console\Commands\Traits\ObtainsProvincesIdsByProvinceName;
use Hash;
use Illuminate\Console\Command;
use Scool\EbreEscoolModel\Person;
use Scool\EbreEscoolModel\User;

/**
 * Class SeedPeople.
 *
 * @package App\Console\Commands
 */
class SeedPeople extends Command
{
    use ObtainsProvincesIdsByProvinceName,
        ObtainsLocationIdsByPersonalInfo;
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
        seed_identifiers();
        seed_contacts();
        seed_locations();
        seed_addresses();
        seed_photos();

        $persons = Person::all();

        foreach ($persons as $person) {
            $name = trim($this->calculateName($person->person_givenName, $person->person_sn1, $person->person_sn2));
            $givenName = trim($person->person_givenName);
            $surname = trim($this->calculateSurname($person->person_sn1, $person->person_sn2));
            $surname1 = trim($person->person_sn1);
            $surname2 = trim($person->person_sn2);
            $birthdate = $this->calculateBirthDate(trim($person->date_of_birth));
            $birthplace_id = null;
            $gender = $this->calculateGender(trim($person->person_gender));
            $civil_status = null;
            $notes = trim($person->person_notes);

            dump('Adding person ' . $name);

            $newPerson = first_or_create_people(
                $name,
                $givenName,
                $surname,
                $surname1,
                $surname2,
                $birthdate,
                $birthplace_id,
                $gender,
                $civil_status,
                $notes
            );
            if (!$newPerson) {
                dump('Skipped. Already exists on database.');
                continue;
            }

            //Person migration info
            PersonMigrationInfo::create([
                'person_id' => $newPerson->id,
                'original_person_id' => $person->person_id,
            ]);

            //Users identifiers
            if ( ! in_array($person->official_id_type, [1,2,3,4]) ) {
                $person->official_id_type = 1;
            }

            $identifier = Identifier::where([
                'value' => $person->person_official_id,
                'type_id'  => $person->person_official_id_type
            ])->first();
            try {
                $newPerson->identifiers()->attach($identifier);
            } catch (\Illuminate\Database\QueryException $qe) {
                if ( !str_contains($qe->getMessage(),'Duplicate entry') ) dump('Exception: '. $qe->getMessage());
            }


            if ( ! in_array($person->person_secondary_official_id_type, [1,2,3,4]) ) {
                $person->person_secondary_official_id_type = 1;
            }

            $identifier2 = Identifier::where([
                'value' => $person->person_secondary_official_id_type,
                'type_id'  => $person->person_secondary_official_id_type
            ])->first();
            try {
                $newPerson->identifiers()->attach($identifier2);
            } catch (\Illuminate\Database\QueryException $qe) {
                if ( !str_contains($qe->getMessage(),'Duplicate entry') ) dump('Exception: '. $qe->getMessage());
            }

            // Contacts

            // Corporative Email
            if ( $person->person_email ) {
                $corporativeEmail = Contact::where([
                    'value' => $person->person_email,
                    'contact_type_id'  => 2
                ])->first();
                try {
                    $newPerson->contacts()->attach($corporativeEmail, ['order' => 1]);
                } catch (\Illuminate\Database\QueryException $qe) {
                    if ( !str_contains($qe->getMessage(),'Duplicate entry') ) dump('Exception: '. $qe->getMessage());
                }
            }

            // Personal Email
            if ( $person->person_secondary_email ) {
                $personalEmail = Contact::where([
                    'value' => $person->person_secondary_email,
                    'contact_type_id'  => 2
                ])->first();
                try {
                    $newPerson->contacts()->attach($personalEmail, ['order' => 2]);
                } catch (\Illuminate\Database\QueryException $qe) {
                    if ( !str_contains($qe->getMessage(),'Duplicate entry') ) dump('Exception: '. $qe->getMessage());
                }
            }

            // Terciary email
            if ( $person->person_terciary_email ) {
                $thirdEmail = Contact::where([
                    'value' => $person->person_terciary_email,
                    'contact_type_id'  => 2
                ])->first();
                try {
                    $newPerson->contacts()->attach($thirdEmail, ['order' => 3]);
                } catch (\Illuminate\Database\QueryException $qe) {
                    if ( !str_contains($qe->getMessage(),'Duplicate entry') ) dump('Exception: '. $qe->getMessage());
                }
            }

            //Phones
            if ( $person->person_mobile ) {
                $mobile = Contact::where([
                    'value' => $person->person_mobile,
                    'contact_type_id'  => 1
                ])->first();
                try {
                    $newPerson->contacts()->attach($mobile, ['order' => 1]);
                } catch (\Illuminate\Database\QueryException $qe) {
                    if ( !str_contains($qe->getMessage(),'Duplicate entry') ) dump('Exception: '. $qe->getMessage());
                }
            }
            if ( $person->person_telephoneNumber ) {
                $phone = Contact::where([
                    'value' => $person->person_telephoneNumber,
                    'contact_type_id'  => 1
                ])->first();
                try {
                    $newPerson->contacts()->attach($phone, ['order' => 2]);
                } catch (\Illuminate\Database\QueryException $qe) {
                    if ( !str_contains($qe->getMessage(),'Duplicate entry') ) dump('Exception: '. $qe->getMessage());
                }
            }

            //Addresses
            if ($person->person_homePostalAddress) {
                $location = $this->obtainLocationIdByPersonalInfo($person);
                $province = $this->obtainProvinceIdByProvinceName($person->state);
                $country_code = "ESP";
                $address = null;
                if ($location != null) {
                    $address = Address::where([
                        'fullname' => $person->person_homePostalAddress,
                        'location' => $location,
                        'province_id' => $province,
                        'country_code' => $country_code
                    ])->first();
                } else {
                    $address = Address::where([
                        'fullname' => $person->person_homePostalAddress,
                        'country_code' => $country_code
                    ])->first();
                }
                try {
                    $newPerson->addresses()->attach($address);
                } catch (\Illuminate\Database\QueryException $qe) {
                    if ( !str_contains($qe->getMessage(),'Duplicate entry') ) dump('Exception: '. $qe->getMessage());
                }
            }

            // Seed user
            if (! $person->person_email ) {
                dump('Skipping user without email: ' . $name);
                continue;
            }
            $email = $person->person_email;
            $passwords =  $this->generateInitialPassword();
            $password = $passwords['hashed_password'];
            $initialPassword = $passwords['password'];

            $user = first_or_create_user(
                $name,
                $email,
                $password,
                $initialPassword
            );
            try {
                $newPerson->users()->attach($user);
            } catch (\Illuminate\Database\QueryException $qe) {
                if ( !str_contains($qe->getMessage(),'Duplicate entry') ) dump('Exception: '. $qe->getMessage());
            }

            //User migration info
            if ($user) {
                $ebreescoolUser = User::where([ 'person_id' => $person->person_id])->first();
                if ($ebreescoolUser) {
                    UserMigrationInfo::create([
                        'user_id' => $user->id,
                        'original_user_id' => $ebreescoolUser->id
                ]);
                }
            }

            //Photos
            if ($person->person_photo) {
                $photo = Photo::where([
                    'storage' => 'local_photos',
                    'path'    => 'photos/' . $person->person_photo
                ])->first();
                $photo->order = 1;
                $photo->save();
                $newPerson->photos()->save($photo);
            }
        }
    }

    /**
     * Generate initial password.
     *
     * @return array
     */
    protected function generateInitialPassword() {
        $hashed_password = Hash::make($password = str_random(8));
        return [
            'password' => $password,
            'hashed_password' => $hashed_password
        ];
    }

    /**
     * Calculate date of birth.
     *
     * @param $date
     * @return null
     */
    protected function calculateBirthDate($date)
    {
        if ( $date == "0000-00-00" ) return null;
        if ( ! $date) return null;
        if ( $date == "") return null;
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
