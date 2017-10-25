<?php

namespace Tests\Feature;

use App;
use App\User;
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
            'civil_status' => $person->civil_status,
            'notes' => $person->notes,
            'updated_at' => $person->updated_at->toDateTimeString(),
            'created_at' => $person->created_at->toDateTimeString(),
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