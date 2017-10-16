<?php

namespace Tests\Feature;

use App;
use App\User;
use Illuminate\Http\UploadedFile;
use Storage;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\CanSignInAsRelationshipsManager;
use Tests\Traits\CheckJsonAPIUriAuthorization;

/**
 * Class UserPhotoTest.
 *
 * @package Tests\Feature
 */
class UserPhotoTest extends TestCase
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
     * Test authorization for update person photo.
     *
     * @test
     * @return void
     */
    public function check_authorization_uri_to_store_photo()
    {
        $user = create(User::class);
        $uri = "/api/v1/user/". $user->id . "/photo";
        $attributes = [ 'photo' => UploadedFile::fake()->image('photo.png')];
        $this->unauthorized_user_cannot_browse_uri($uri, 'POST' ,$attributes);
        $this->authorized_user_can_browse_uri_api($uri, 'POST', $attributes);

        $otherUser = create(User::class);
        $uri = "/api/v1/user/". $otherUser->id . "/photo";
        $this->an_user_cannot_browse_uri_api($uri,'POST',$attributes);

    }

    /**
     * Test store photo user.
     *
     * @test
     * @return void
     */
    public function test_store()
    {

        Storage::fake('local');

        $user = create(User::class);

        $this->signInAsRelationshipsManager('api');
        $response = $this->json('POST', '/api/v1/user/' . $user->id . '/photo', [
            'photo' => UploadedFile::fake()->image('photo.png')
        ]);

        $path = json_decode($response->getContent())->path;

        $this->assertTrue(ends_with($path, '-' . $user->person->id . '-.png'));

        Storage::disk('local')->assertExists($path);

        $this->assertDatabaseHas('photos', [
            'storage' => 'local',
            'path' => $path,
            'person_id' => $user->person->id,
        ]);
    }
}
