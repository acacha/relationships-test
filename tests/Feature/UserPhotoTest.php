<?php

namespace Tests\Feature;

use Acacha\Relationships\Models\Person;
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

    const AVATAR_PATH = 'node_modules/admin-lte/dist/img/avatar.png';
    const AVATAR2_PATH = 'node_modules/admin-lte/dist/img/avatar2.png';
    const AVATAR3_PATH = 'node_modules/admin-lte/dist/img/avatar3.png';
    const AVATAR4_PATH = 'node_modules/admin-lte/dist/img/avatar5.png';

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
        $attributes = [ 'file' => UploadedFile::fake()->image('photo.png')];
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
            'file' => UploadedFile::fake()->image('photo.png')
        ]);
        $path = json_decode($response->getContent())->path;

        $this->assertTrue(ends_with($path, '-' . $user->person->id . '-.png'));

        Storage::disk('local')->assertExists($path);

        $this->assertDatabaseHas('photos', [
            'storage' => 'local',
            'origin' => 'photo.png',
            'path' => $path,
            'person_id' => $user->person->id,
        ]);
    }

    /**
     *
     * @test
     */
    public function list_all_user_photos_for_manager()
    {
        $user = $this->createUserWithFoto();
        add_photo_to_user($user, base_path(self::AVATAR2_PATH));
        add_photo_to_user($user, base_path(self::AVATAR3_PATH));
        add_photo_to_user($user, base_path(self::AVATAR4_PATH));
        $this->signInAsRelationshipsManager('api', $user);
        $response = $this->json('GET', '/api/v1/user/' . $user->id . '/photos');
        $response->assertSuccessful();
        $response->assertJsonStructure([
            [
                'id', 'storage','origin','path','person_id','created_at','updated_at'
            ],
        ]);

    }


    /**
     * An user can list all his own person photos
     * @test
     */
    public function list_all_user_photos()
    {
        $user = $this->createUserWithFoto();
        add_photo_to_user($user, base_path(self::AVATAR2_PATH));
        add_photo_to_user($user, base_path(self::AVATAR3_PATH));
        add_photo_to_user($user, base_path(self::AVATAR4_PATH));
        $this->signIn($user, 'api');
        $response = $this->json('GET', '/api/v1/user/' . $user->id . '/photos');
        $response->assertSuccessful();
        $response->assertJsonStructure([
            [
                'id', 'storage','origin','path','person_id','created_at','updated_at'
            ],
        ]);
    }

    /**
     * Get default photo path.
     *
     * @param $photoPath
     * @return string
     */
    protected function defaultPhotoPath($photoPath)
    {
        if ($photoPath == null) return base_path(self::AVATAR_PATH);
        return $photoPath;
    }

    /**
     * Create user with photo.
     *
     * @return mixed
     */
    protected function createUserWithFoto($photoPath = null)
    {
        $photoPath = $this->defaultPhotoPath($photoPath);
        $person = $this->createPersonWithPhoto($photoPath);
        $person->users()->attach(factory(App\User::class)->create());
        return $person->users()->first();
    }

    /**
     * Create person with photo.
     *
     * @return mixed
     */
    protected function createPersonWithPhoto($photoPath = null)
    {
        $photoPath = $this->defaultPhotoPath($photoPath);
        $person = create(Person::class);
        add_photo_to_person($photoPath, $person);
        return $person;
    }

    /**
     * Check list all user photos authorization
     */
    public function check_list_all_user_photos_authorization()
    {
        $method = 'GET';
        $this->unauthorized_user_cannot_browse_uri('/api/v1/user/1/photos', $method);

        $user = $this->createUserWithFoto();
        $uri = '/api/v1/user/' . $user->id . '/photos';
        $this->authorized_user_can_browse_uri_api($uri, $method);

        $this->signIn($user,'api');
        $response = $this->json(strtoupper($method),$uri);
        $response->assertStatus(200);

        $otherUser = $this->createUserWithFoto(base_path(self::AVATAR2_PATH));
        $uri = '/api/v1/user/' . $otherUser->id . '/photos';
        $this->an_user_cannot_browse_uri_api($uri, $method, [], $user);
    }
}
