<?php

namespace Tests\Feature;

use Acacha\Relationships\Models\Identifier;
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
     * Search can find a complete value.
     *
     * @test
     */
    public function search_can_find_a_complete_value()
    {
        seed_identifier_types();
        seed_random_nif_identifiers(10);
        $this->signInAsRelationshipsManager('api');
        $response  = $this->json('GET','api/v1/identifier');
        $response->assertSuccessful();
        $response->assertJsonStructure([
            [
                'id',
                'value',
                'type_id'
            ],
        ]);
    }


}