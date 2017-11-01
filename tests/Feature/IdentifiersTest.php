<?php

namespace Tests\Feature;

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
     * Show all identifiers.
     *
     * @test
     */
    public function show_all_identifiers()
    {
        seed_identifier_types();
        create_person_with_nif();
        create_person_with_nif();
        create_person_with_nif();

        $this->signInAsRelationshipsManager('api');
        $response  = $this->json('GET','api/v1/identifier');
        $response->assertSuccessful();
        $response->assertJsonStructure([
            [
                'id',
                'value',
                'type_id',
                'type_name',
                'person_id'
            ],
        ]);
    }

    /**
     * Check authorization uri to show all identifiers.
     *
     * @test
     * @return void
     */
    public function check_authorization_uri_to_show_all_identifiers()
    {
        $this->check_json_api_uri_authorization('api/v1/identifier');
    }

}