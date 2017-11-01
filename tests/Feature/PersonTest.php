<?php

namespace Tests\Feature;

use Acacha\Relationships\Models\Identifier;
use Acacha\Relationships\Models\IdentifierType;
use App;
use App\User;
use Faker\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\CanSignInAsRelationshipsManager;
use Tests\Traits\CheckJsonAPIUriAuthorization;

/**
 * Class PersonTest.
 *
 * @package Tests\Feature
 */
class PersonTest extends TestCase
{
    use CheckJsonAPIUriAuthorization,
        CanSignInAsRelationshipsManager,
        RefreshDatabase;
    /**
     * Set up tests.
     */
    public function setUp()
    {
        parent::setUp();
        App::setLocale('en');
        initialize_relationships_management_permissions();
//        $this->withoutExceptionHandling();
    }

    /**
     * Show person.
     *
     * @test
     * @return void
     */
    public function show_person()
    {
        $person = create_person_with_nif();
        $user = create(User::class);
        $this->signIn($user,'api');
        $user->persons()->save($person);

        $response = $this->get('api/v1/person/' . $person->id);
        $response->assertSuccessful();

        $response->assertJson([
            'id' => $person->id,
            'givenName' => $person->givenName,
            'surname1' => $person->surname1,
            'surname2' => $person->surname2,
            'birthdate' => $person->birthdate,
            'birthplace-name' => $person->birthplace_name,
            'birthplace_id' => $person->birthplace_id,
            'gender' => $person->gender,
            'identifier' => $person->identifier,
            'identifier-id' => $person->identifier_id,
            'identifier-type' => $person->identifier_type,
            'civil_status' => $person->civil_status,
            'notes' => $person->notes,
            'updated_at' => $person->updated_at->toDateTimeString(),
            'created_at' => $person->created_at->toDateTimeString(),
        ]);
    }

    /**
     * Create person.
     *
     * @test
     * @return void
     */
    public function create_person()
    {
        $faker = Factory::create('es_ES');

        $type = IdentifierType::create([
            'name' => 'NIF'
        ]);

        $identifier = Identifier::create([
            'value' => $faker->dni,
            'type_id' => $type->id
        ]);

        dd($identifier);

        $gender = $faker->randomElements(['male', 'female']);
        $givenName = $faker->firstName($gender[0]);
        $surname1 = $faker->lastName;
        $surname2 = $faker->lastName;

        $person = [
            'identifier_id' => 1,
            'givenName' => $givenName,
            'identifier_id' => 1,
            'surname1' => $surname1,
            'surname2' => $surname2,
            'birthdate' => '',
            'birthplace_id' => 1,
            'gender' => $gender[0],
        ];

        dump($person);

        $this->signInAsRelationshipsManager('api');

        $response = $this->json('POST','api/v1/person/', $person);

        $response->assertSuccessful();

        $this->assertDatabaseHas('people', [
            'givenName' => $person['givenName'],
            'surname1' => $person['surname1'],
            'surname2' => $person['surname2'],
            'birthdate' => $person['birthdate'],
            'birthplace_id' => $person['birthplace_id'],
            'gender' => $person['gender'],
        ]);

        //TODO: check identifier is added

//        $table->string('givenName')->nullable();
//        $table->string('surname1')->nullable();
//        $table->string('surname2')->nullable();
//        $table->date('birthdate')->nullable();
//        $table->integer('birthplace_id')->unsigned()->nullable();
//        $table->enum('gender',['male','female'])->nullable();
//        $table->enum('civil_status',['Soltero/a','Casado/a','Separado/a','Divorciado/a','Viudo/a'])->nullable();
//        $table->string('notes')->nullable();
//        $table->enum('state',['draft','valid','completed'])->default('draft');
//        $table->timestamps();

        $response->assertJson([
            'givenName' => $person['givenName'],
            'surname1' => $person['surname1'],
            'surname2' => $person['surname2'],
            'birthdate' => $person['birthdate'],
            'birthplace_id' => $person['birthplace_id'],
            'gender' => $person['gender'],
            'identifier-id' => $person['identifier_id'],
        ]);


    }

    /**
     * Check authorization uri to show a person.
     *
     * @test
     * @return void
     */
    public function check_authorization_uri_to_show_all_identifiers()
    {
        $person = create_person_with_nif();
        $this->check_json_api_uri_authorization('api/v1/person/' . $person->id);
    }
}