<?php

namespace Tests\Feature;

use Acacha\Relationships\Models\Identifier;
use App;
use Faker\Factory;
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
     * Search can find a complete value.
     *
     * @test
     */
    public function search_can_find_a_complete_value()
    {
        seed_identifier_types();
        seed_random_nif_identifiers(10);
        $identifier = Identifier::first();
        $this->signInAsRelationshipsManager('api');
        $response  = $this->json('GET','api/v1/identifier/search?q=' . $identifier->value);
        $response->assertSuccessful();
        $response->assertJson([
            [
                'id' => $identifier->id,
                'value' => $identifier->value,
                'type_id' => $identifier->type_id
            ],
        ]);
    }

    /**
     * Search can find a complete value.
     *
     * @test
     */
    public function search_can_find_a_partial_value()
    {
        $idType = first_or_create_identifier_type('NIF');
        $faker = Factory::create('es_ES');
        $identifier = Identifier::create([
           'value' => $faker->unique()->dni,
           'type_id' => $idType->id
        ]);
//        dd($identifier->id);
        $this->signInAsRelationshipsManager('api');
        $response  = $this->json('GET','api/v1/identifier/search?q=' . substr($identifier->value,0,5));
        $response->assertSuccessful();
        $response->assertJson([
            [
                'id' => $identifier->id,
                'value' => $identifier->value,
                'type_id' => $identifier->type_id
            ],
        ]);
    }

    /**
     * No identifiers found.
     * @test
     */
    public function no_identifiers_found()
    {
        seed_identifier_types();
        $this->signInAsRelationshipsManager('api');
        $response  = $this->json('GET','api/v1/identifier/search?q=1426');
        $response->assertSuccessful();
        $response->assertJson(
            [
                'error' => 'No identifiers found, please try with different keywords.'
            ]
        );
    }

    /**
     * Q parameter is requires in search
     * @test
     */
    public function q_parameter_is_required_in_search()
    {
        $this->signInAsRelationshipsManager('api');
        $response  = $this->json('GET','api/v1/identifier/search');
        $response->assertStatus(422);
    }
}