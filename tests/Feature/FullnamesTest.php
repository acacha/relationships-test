<?php

namespace Tests\Feature;

use Acacha\Relationships\Models\Identifier;
use Acacha\Relationships\Models\IdentifierType;
use Acacha\Relationships\Models\Person;
use App;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\CanSignInAsRelationshipsManager;
use Tests\Traits\CheckJsonAPIUriAuthorization;


/**
 * Class IdentifierTest.
 *
 * @package Tests\Feature
 */
class IdentifierTest extends TestCase
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
     * Check authorization uri to show all identifiers.
     *
     * @test
     * @return void
     */
    public function check_authorization_uri_to_show_all_identifiers()
    {
        $this->check_json_api_uri_authorization('api/v1/fullname');
    }

    /**
     * Show all fullnames.
     *
     * @test
     */
    public function show_all_fullnames()
    {
        $person = create_person_with_nif();
        $this->signInAsRelationshipsManager('api');
        $response  = $this->json('GET','api/v1/fullname');
//        $response->dump();
        $response->assertSuccessful();
        $response->assertJsonStructure([[
            'name',
            'identifier',
            'id'
        ]]);
        $response->assertJson([[
            'name' => $person->name,
            'identifier' => $person->identifier,
            'id' => $person->id
        ]]);
    }


}