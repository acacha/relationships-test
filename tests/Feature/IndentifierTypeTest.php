<?php

namespace Tests\Feature;

use App;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\CanSignInAsRelationshipsManager;
use Tests\Traits\CheckJsonAPIUriAuthorization;


/**
 * Class IndentifierTypeTest.
 *
 * @package Tests\Feature
 */
class IndentifierTypeTest extends TestCase
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
     * @test
     */
    public function index()
    {
        seed_identifier_types();
        $this->signInAsRelationshipsManager('api');
        $response  = $this->json('GET','api/v1/identifierType');
        $response->assertSuccessful();
        $response->assertJsonStructure([
            [
                'id', 'name','created_at','updated_at'
            ],
        ]);
    }
}