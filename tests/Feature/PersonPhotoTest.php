<?php

namespace Tests\Feature;

use Acacha\Relationships\Models\Person;
use App;
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
 * @package Tests\Feature
 */
class PersonPhotoTest extends TestCase
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
     * Test person is not found.
     *
     * @test
     * @return void
     */
    public function person_is_not_found()
    {
        $this->signInAsRelationshipsManager('api');
        $response = $this->json('POST','/api/v1/person/1/photo', [
            'photo' => UploadedFile::fake()->image('photo.png')
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
        $this->check_json_api_uri_authorization("/api/v1/person/". $person->id . "/photo",'post');
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
     * Test an user cannot see others photos.
     *
     * @test
     * @return void
     */
    public function and_user_cannot_see_others_photos()
    {
        $user = $this->createUserWithFoto();
        $this->signIn($user);
        $person = $this->createPersonWithPhoto('user_photos/testphoto1.png');
        $response = $this->json('GET','/person/' . $person->id .'/photo');
        $response->assertStatus(403);
        Storage::delete($person->photos()->first()->path);
        Storage::delete('user_photos/testphoto1.png');
    }

    /**
     * Person photo can be stored if already exists one.
     *
     * @test
     * @group prova
     * @return void
     */
    public function person_photo_can_be_stored_if_already_exists_one()
    {
        $this->signInAsRelationshipsManager();

        $person = create(Person::class);
        $path1 = Storage::putFileAs('user_photos', UploadedFile::fake()->image('testphoto.png'), 'testphoto1.png');
        $person->photos()->create([
            'storage' => 'local',
            'path' => 'user_photos/testphoto1.png'
        ]);

        Storage::fake('local');

        $person = create(Person::class);
        $response = $this->json('POST', '/api/v1/person/' . $person->id . '/photo', [
            'photo' => UploadedFile::fake()->image('testphoto2.png')
        ]);
        $path2 = json_decode($response->getContent())->path;

        Storage::disk('local')->assertExists($path1);
        Storage::disk('local')->assertExists($path2);

        $this->assertDatabaseHas('photos', [
            'storage' => 'local',
            'path' => $path1,
            'person_id' => $person->id,
        ]);

        $this->assertDatabaseHas('photos', [
            'storage' => 'local',
            'path' => $path2,
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
     * @group working
     * @test
     * @return void
     */
    public function test_store()
    {
        Storage::fake('local');

        $person = create(Person::class);

        $this->signInAsRelationshipsManager('api');
        $response = $this->json('POST', '/api/v1/person/' . $person->id . '/photo', [
            'photo' => UploadedFile::fake()->image('photo.png')
        ]);

        $path = json_decode($response->getContent())->path;

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
        $this->check_json_api_uri_authorization("/api/v1/person/" . $person->id . "/photo",'get');
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

        $person->photos()->create([
            'storage' => 'local',
            'path' => 'user_photos/testphoto.png'
        ]);

        Storage::delete('user_photos/testphoto.png');

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
        $path = Storage::putFileAs('user_photos', UploadedFile::fake()->image('testphoto.png'), 'testphoto.png');
        $person->photos()->create([
            'storage' => 'local',
            'path' => 'user_photos/testphoto.png'
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
        $this->check_json_api_uri_authorization("/api/v1/person/" . $person->id . "/photo",'put');
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

        $person = create(Person::class);

        $this->signInAsRelationshipsManager('api');
        $response = $this->json('PUT', '/api/v1/person/' . $person->id . '/photo', [
            'photo' => UploadedFile::fake()->image('photo.png')
        ]);

        $path = json_decode($response->getContent())->path;

        Storage::disk('local')->assertExists($path);

        $this->assertDatabaseHas('photos', [
            'storage' => 'local',
            'path' => $path,
            'person_id' => $person->id,
        ]);

    }

    /**
     * Create person with photo.
     *
     * @return mixed
     */
    protected function createPersonWithPhoto($photoPath = 'user_photos/testphoto.png')
    {
        $person = create(Person::class);
        $this->attachPhoto($photoPath, $person);
        return $person;
    }

    /**
     * Create person with multiple photos.
     */
    protected function createPersonWithPhotos()
    {
        $person = create(Person::class);
        $this->attachPhoto('user_photos/testphoto.png', $person);
        $this->attachPhoto('user_photos/testphoto1.png', $person);
        $this->attachPhoto('user_photos/testphoto2.png', $person);
        return $person;
    }

    /**
     * Create user with photo.
     *
     * @return mixed
     */
    protected function createUserWithFoto()
    {
        $person = $this->createPersonWithPhoto();
        $person->users()->attach(factory(App\User::class)->create());
        return $person->users()->first();
    }

    /**
     * @param $photoPath
     * @param $person
     */
    protected function attachPhoto($photoPath, $person)
    {
        Storage::putFileAs('user_photos', UploadedFile::fake()->image('testphoto.png'), 'testphoto.png');
        $person->photos()->create([
            'storage' => 'local',
            'path' => $photoPath
        ]);
    }
}
