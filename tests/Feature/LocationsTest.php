<?php

namespace Tests\Feature;

use Acacha\Relationships\Models\Location;
use App;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\CanSignInAsRelationshipsManager;
use Tests\Traits\CheckJsonAPIUriAuthorization;

/**
 * Class LocationsTest.
 *
 * @package Tests\Feature
 */
class LocationsTest extends TestCase
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
     * Show all locations.
     *
     * @test
     */
    public function show_all_locations()
    {
        factory(Location::class,5)->create();

        $this->signInAsRelationshipsManager('api');
        $response  = $this->json('GET','api/v1/location');
        $response->assertSuccessful();
        $response->assertJsonStructure([
            [
                'id',
                'name',
                'postalcode'
            ],
        ]);
    }

    /**
     * Check authorization uri to show all locations.
     *
     * @test
     * @return void
     */
    public function check_authorization_uri_to_show_all_locations()
    {
        $this->check_json_api_uri_authorization('api/v1/location');
    }

}