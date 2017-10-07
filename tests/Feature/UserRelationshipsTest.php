<?php

namespace Tests\Feature;

use Acacha\Relationships\Models\Person;
use App;
use App\User;
use Illuminate\Http\UploadedFile;
use Storage;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\CanSignInAsRelationshipsManager;
use Tests\Traits\CheckJsonAPIUriAuthorization;

/**
 * Class UserRelationshipsTest.
 *
 * @package Tests\Feature
 */
class UserRelationshipsTest extends TestCase
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
     * Photo fiels is required when storing.

     * @test
     * @return void
     */
    public function photo_field_is_required_when_storing()
    {
        $this->signInAsRelationshipsManager('api');
        $response = $this->json('POST','/api/v1/person/1/photo');
        $response->assertStatus(422);
        $response->assertJson([
            'message' => 'The given data was invalid.',
            'errors' => [
                'photo' => [
                    'The photo field is required.'
                ]
            ]
        ]);
    }

    /**
     * asddsa
     * @group caca
     *
     * @test
     * @return void
     */
    public function todo()
    {
        $this->signInAsRelationshipsManager('api');
        $user = factory(User::class)->create();
        $response = $this->json('GET','/api/v1/user_relationships/' . $user->id);
        $response->dump();
        $response->assertStatus(422);
        $response->assertJson([
            'message' => 'The given data was invalid.',
            'errors' => [
                'photo' => [
                    'The photo field is required.'
                ]
            ]
        ]);
    }

    /**
     * Check returns 404 for non existing user id
     *
     * @test
     * @return void
     */
    public function check_returns_404_for_non_existing_id()
    {
        $this->signInAsRelationshipsManager('api');
        $response = $this->json('GET','/api/v1/user_relationships/999');
        $response->assertStatus(404);
    }

    /**
     * Test authorization for URI /api/v1/person/{id}/photo.
     *
     * @test
     * @return void
     */
    public function check_authorization_uri_to_store_photo()
    {
        $this->check_json_api_uri_authorization("/api/v1/user_relationships",'get');
    }

    /**
     * Test authorization for URI /api/v1/person/{id}/photo.
     * @group shit
     * @test
     * @return void
     */
    public function check_authorization_uri_to_store_photo_specific_user_id()
    {
        $this->check_json_api_uri_authorization("/api/v1/user_relationships/1",'get');
    }

}
