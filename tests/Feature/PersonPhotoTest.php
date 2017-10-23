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
 * Class PersonPhotoTest.
 *
 * Please run npm install before executing this tests
 *
 * @package Tests\Feature
 */
class PersonPhotoTest extends TestCase
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
                'file' => [
                    'The file field is required.'
                ]
            ]
        ]);
    }

    /**
     * Test person is not found.
     *
     * @test
     * @return void
     */
    public function person_is_not_found()
    {
        $this->signInAsRelationshipsManager('api');
        $response = $this->json('POST','/api/v1/person/1/photo', [
            'file' => UploadedFile::fake()->image('photo.png')
        ]);

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
        $person = create(Person::class);
        $otherPerson = create(Person::class);

        $uri = "/api/v1/person/". $person->id . "/photo";
        $attributes = [ 'file' => UploadedFile::fake()->image('photo.png')];
        $this->unauthorized_user_cannot_browse_uri($uri, 'POST' ,$attributes);
        $user = create(User::class);
        $this->signIn($user,'api');
        $user->persons()->attach($otherPerson);
        $response = $this->json('POST',$uri, $attributes);
        $response->assertStatus(403);

        $this->authorized_user_can_browse_uri_api($uri, 'POST', $attributes);

    }

    /**
     * Test authorization for URI /api/v1/person/{id}/photo.
     *
     * @test
     * @return void
     */
    public function check_person_not_associated_to_user_returns_404()
    {
        //Given a person
        $person = create(Person::class);
        //Given a user
        $this->signIn(null,'api');
        $response = $this->json('POST',"/api/v1/person/". $person->id . "/photo");
        //The person is not associated to user so 404 not found
        $response->assertStatus(404);
    }

    /**
     * Test an user can see his own photos.
     *
     * @test
     * @return void
     */
    public function and_user_can_show_owned_photos()
    {
        $user = $this->createUserWithFoto();
        $this->signIn($user);
        $person = $user->persons()->first();
        $response = $this->json('GET','/person/' . $person->id .'/photo');
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'image/png');

        Storage::delete($person->photos()->first()->path);
    }

    /**
     * Test an user without personal info cannot see his own photos (404 error).
     *
     * @test
     * @return void
     */
    public function and_user_without_personal_info_cannot_show_owned_photos()
    {
        $user = create(User::class);
        $this->signIn($user);
        $response = $this->json('GET','/person/1/photo');
        $response->assertStatus(404);
    }

    /**
     * Test an user cannot see others photos.
     *
     * @test
     * @return void
     */
    public function and_user_cannot_see_others_photos()
    {
        $user = $this->createUserWithFoto();
        $this->signIn($user);
        $filename = $this->randomFileName();
        $person = $this->createPersonWithPhoto();
        $response = $this->json('GET','/person/' . $person->id .'/photo');
        $response->assertStatus(403);
        Storage::delete($person->photos()->first()->path);
        Storage::delete('user_photos/' . $filename . '.png');
    }

    /**
     * Person photo can be stored if already exists one.
     *
     * @test
     * @return void
     */
    public function person_photo_can_be_stored_if_already_exists_one()
    {
        $this->signInAsRelationshipsManager('api');

        $person = create(Person::class);
        Storage::fake('local');
        $filename1 = $this->randomFileName();
        $response = $this->json('POST', '/api/v1/person/' . $person->id . '/photo', [
            'file' => UploadedFile::fake()->image($filename1 . '.png')
        ]);
        $path1 = json_decode($response->getContent())->path;
        $filename2 = $this->randomFileName();

        $response = $this->json('POST', '/api/v1/person/' . $person->id . '/photo', [
            'file' => UploadedFile::fake()->image($filename2. '.png')
        ]);
        $path2 = json_decode($response->getContent())->path;

        Storage::disk('local')->assertExists($path1);
        Storage::disk('local')->assertExists($path2);

        $this->assertDatabaseHas('photos', [
            'storage' => 'local',
            'path' => $path1,
            'origin' => $filename1. '.png',
            'person_id' => $person->id,
        ]);

        $this->assertDatabaseHas('photos', [
            'storage' => 'local',
            'path' => $path2,
            'origin' => $filename2. '.png',
            'person_id' => $person->id,
        ]);

        // Assert testphoto2 is the active one
        $activePerson = $person->photos()->first();
        $this->assertTrue( $activePerson->path == $path1);

        //Assert the are 2 photos.
        $this->assertTrue($person->photos()->count() == 2);

        Storage::delete($path1);
        Storage::delete($path2);
    }

    /**
     * Test store photo person.
     *
     * @test
     * @return void
     */
    public function test_store()
    {

        Storage::fake('local');

        $person = create(Person::class);

        $this->signInAsRelationshipsManager('api');
        $response = $this->json('POST', '/api/v1/person/' . $person->id . '/photo', [
            'file' => UploadedFile::fake()->image('photo.png')
        ]);

        $path = json_decode($response->getContent())->path;

        $this->assertTrue(ends_with($path, '-' . $person->id . '-' . snake_case($person->name) .'.png'));

        Storage::disk('local')->assertExists($path);

        $this->assertDatabaseHas('photos', [
            'storage' => 'local',
            'path' => $path,
            'person_id' => $person->id,
        ]);
    }

    /**
     * Test authorization for show person photo.
     *
     * @test
     * @return void
     */
    public function check_authorization_uri_to_show_photo()
    {
        $person = $this->createPersonWithPhoto();
        $otherPerson= create(Person::class);

        $uri = "/api/v1/person/". $person->id . "/photo";
        $attributes = [ 'file' => UploadedFile::fake()->image('photo.png')];
        $this->unauthorized_user_cannot_browse_uri($uri, 'GET' ,$attributes);

        $user = create(User::class);
        $user->persons()->attach($otherPerson);
        $this->signIn($user,'api');
        $response = $this->json('GET',$uri, $attributes);
        $response->assertStatus(403);

        $this->authorized_user_can_browse_uri_api($uri, 'GET', $attributes);

    }

    /**
     * Person photo 404 if person not exists api.
     *
     * @test
     */
    public function person_photo_404_is_person_not_exists_api()
    {
        $this->signInAsRelationshipsManager('api');
        $response = $this->json('GET','/api/v1/person/99/photo');

        $response->assertStatus(404);

    }

    /**
     * Person photo 404 if person not exists.
     *
     * @test
     */
    public function person_photo_404_is_person_not_exists()
    {
        $this->signInAsRelationshipsManager();
        $response = $this->json('GET','/person/99/photo');
        $response->assertStatus(404);
    }

    /**
     * Person photo is not found for and user without photo api.
     *
     * @test
     */
    public function person_photo_is_not_shown_for_an_user_without_photo_api()
    {
        $this->signInAsRelationshipsManager('api');
        $person = create(Person::class);
        $response = $this->json('GET','/api/v1/person/' . $person->id .'/photo');
        $response->assertStatus(404);

    }

    /**
     * Person photo is not found for and user without photo.
     *
     * @test
     */
    public function person_photo_is_not_shown_for_an_user_without_photo()
    {
        $this->signInAsRelationshipsManager();
        $person = create(Person::class);
        $response = $this->get('/person/' . $person->id .'/photo');
        $response->assertStatus(404);

    }

    /**
     * Person photo throws file not found exception api.
     *
     * @test
     */
    public function person_photo_throws_file_not_found_exception_api()
    {
        $this->signInAsRelationshipsManager();
        $person = create(Person::class);

        $filename = $this->randomFileName();
        $person->photos()->create([
            'storage' => 'local',
            'path' => 'user_photos/' . $filename . '.png',
            'origin' => basename('user_photos/' . $filename . '.png')
        ]);

        Storage::delete('user_photos/' . $filename . '.png');

        $this->expectException(FileNotFoundException::class);
        $this->withoutExceptionHandling();
        $this->get('/person/' . $person->id .'/photo');
        $this->withExceptionHandling();

    }

    /**
     * Person photo is not found for and user without photo api.
     *
     * @test
     */
    public function person_photo_is_shown()
    {
        $this->signInAsRelationshipsManager();
        $person = create(Person::class);
        $filename = $this->randomFileName() . '.png';
        $path = Storage::putFileAs('user_photos', UploadedFile::fake()->image( $filename ), $filename);
        $person->photos()->create([
            'storage' => 'local',
            'path' => 'user_photos/' . $filename,
            'origin' => $filename .'.png'
        ]);

        $response = $this->get('/person/' . $person->id .'/photo');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'image/png');

        Storage::delete($path);

    }

    /**
     * Person photo is not found for and user without photo api.
     *
     * @test
     */
    public function person_photo_is_shown_api()
    {
        $this->signInAsRelationshipsManager('api');
        $person = $this->createPersonWithPhoto();

        $response = $this->json('GET','/person/' . $person->id .'/photo');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'image/png');

        Storage::delete($person->photos()->first()->path);

    }

    /**
     * Api shows 404 trying to remove photo from unexisting person.
     *
     * @test
     */
    public function api_show_404_trying_to_remove_photo_from_unexisting_person()
    {
        $this->signInAsRelationshipsManager('api');
        $response = $this->json('DELETE','/api/v1/person/1/photo');
        $response->assertStatus(404);
    }

    /**
     * Api shows 404 trying to remove photo from person without photos.
     *
     * @test
     */
    public function api_show_404_trying_to_remove_photo_from_person_without_photos()
    {
        $this->signInAsRelationshipsManager('api');
        $person = create(Person::class);
        $response = $this->json('DELETE','/api/v1/person/' . $person->id . ' /photo');
        $response->assertStatus(404);
    }

    /**
     * Manager can remove most recent person photo.
     *
     * @test
     */
    public function manager_can_remove_user_photo()
    {
        $this->signInAsRelationshipsManager('api');
        $person = $this->createPersonWithPhoto();
        $response = $this->json('DELETE','/api/v1/person/' . $person->id .'/photo');
        $response->assertStatus(200);

        $path  = json_decode($response->getContent())->path ;

        Storage::disk('local')->assertMissing($path);

        $this->assertDatabaseMissing('photos', [
            'storage' => 'local',
            'path' => $path,
            'person_id' => $person->id,
        ]);
    }

    /**
     * Manager can remove all person photos.
     *
     * @test
     */
    public function manager_can_remove_all_user_photos()
    {
        $this->signInAsRelationshipsManager('api');
        $person = $this->createPersonWithPhotos();
        $response = $this->json('DELETE','/api/v1/person/' . $person->id .'/photo', [
            'all' => true
        ]);
        $response->assertStatus(200);

        $photos  = json_decode($response->getContent());

        foreach ($photos as $photo) {
            Storage::disk('local')->assertMissing($photo->path);

            $this->assertDatabaseMissing('photos', [
                'storage' => 'local',
                'path' => $photo->path,
                'person_id' => $person->id,
            ]);
        }
    }

    /**
     * An user can remove owned photo.
     *
     * @test
     */
    public function an_user_can_remove_owned_photo()
    {
        $user = $this->createUserWithFoto();
        $this->signIn($user, 'api');
        $person = $user->persons()->first();
        $response = $this->json('DELETE','/api/v1/person/' . $person->id .'/photo');
        $response->assertStatus(200);

        $path  = json_decode($response->getContent())->path ;

        Storage::disk('local')->assertMissing($path);

        $this->assertDatabaseMissing('photos', [
            'storage' => 'local',
            'path' => $path,
            'person_id' => $person->id,
        ]);
    }

    /**
     * Test authorization for update person photo.
     *
     * @test
     * @return void
     */
    public function check_authorization_uri_to_update_photo()
    {
        $person = $this->createPersonWithPhoto();
        $otherPerson= create(Person::class);


        $uri = "/api/v1/person/". $person->id . "/photo";
        $attributes = [ 'file' => UploadedFile::fake()->image('photo.png')];
        $this->unauthorized_user_cannot_browse_uri($uri, 'PUT' ,$attributes);
        $user = create(User::class);
        $this->signIn($user,'api');
        $user->persons()->attach($otherPerson);
        $response = $this->json('PUT',$uri, $attributes);
        $response->assertStatus(403);

        $this->authorized_user_can_browse_uri_api($uri, 'PUT', $attributes);
    }

    /**
     * A manager can update photo
     *
     * @test
     * @return void
     */
    public function a_manager_can_update_photo()
    {
        //Same as storage.
        Storage::fake('local');

        $person = $this->createPersonWithPhoto();

        $this->withoutExceptionHandling();

        $this->signInAsRelationshipsManager('api');
        $response = $this->json('PUT', '/api/v1/person/' . $person->id . '/photo', [
            'file' => UploadedFile::fake()->image('photo.png')
        ]);

//        $response->dump();
        $path = json_decode($response->getContent())->path;

        Storage::disk('local')->assertExists($path);

        $this->assertDatabaseHas('photos', [
            'storage' => 'local',
            'path' => $path,
            'person_id' => $person->id,
        ]);
    }


    /**
     * Check authorization for list user photos.
     *
     * @test
     */
    public function check_authorization_for_list_user_photos()
    {
        $method = 'GET';
        $this->unauthorized_user_cannot_browse_uri('/api/v1/person/1/photos', $method);

        $user = $this->createUserWithFoto();
//        dd(Person::findOrFail($user->person->id)->photos()->first());
        $uri = '/api/v1/person/' . $user->person->id . '/photos';
        $this->authorized_user_can_browse_uri_api($uri, $method);

        $this->signIn($user,'api');
        $response = $this->json(strtoupper($method),$uri);
        $response->assertStatus(200);


        $otherUser = $this->createUserWithFoto(base_path(self::AVATAR2_PATH));
        $uri = '/api/v1/person/' . $otherUser->person->id . '/photos';
        $this->an_user_cannot_browse_uri_api($uri, $method, [], $user);

    }

    /**
     * An user can list his own photos.
     *
     * @test
     */
    public function an_user_can_list_his_own_photos()
    {
        $user = $this->createUserWithFoto();
        add_photo_to_user($user, base_path(self::AVATAR2_PATH));
        add_photo_to_user($user, base_path(self::AVATAR3_PATH));
        add_photo_to_user($user, base_path(self::AVATAR4_PATH));

        $this->signIn($user,'api');

        //Execute
        $response = $this->json('GET', '/api/v1/person/' . $user->person->id . '/photos');
        $response->assertSuccessful();
        $response->assertJsonStructure([
            [
                'id', 'storage','origin','path','person_id','created_at','updated_at'
            ],
        ]);

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
     * Create person with multiple photos.
     */
    protected function createPersonWithPhotos()
    {
        $person = create(Person::class);
        add_photo_to_person(base_path(self::AVATAR_PATH), $person);
        add_photo_to_person(base_path(self::AVATAR2_PATH), $person);
        add_photo_to_person(base_path(self::AVATAR3_PATH), $person);
        return $person;
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
     * @return mixed
     */
    protected function randomFileName()
    {
        $faker = \Faker\Factory::create();
        return $faker->unique()->word();
    }
}
