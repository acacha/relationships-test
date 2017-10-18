<?php

namespace Tests\Feature;

use Acacha\Relationships\Models\Person;
use Acacha\Relationships\Models\Photo;
use App;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\File;
use Storage;
use Tests\TestCase;
use Tests\Traits\CanSignInAsRelationshipsManager;
use Tests\Traits\CheckJsonAPIUriAuthorization;

/**
 * Class PhotoTest.
 *
 * Please run npm install before executing this tests
 *
 * @package Tests\Feature
 */
class PhotoTest extends TestCase
{

    const AVATAR_PATH = 'node_modules/admin-lte/dist/img/avatar.png';
    const AVATAR2_PATH = 'node_modules/admin-lte/dist/img/avatar2.png';

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
     * Logged users can see his own photos.
     *
     * @test
     * @return void
     */
    public function logged_users_can_see_his_own_photos()
    {
        $user = factory(User::class)->create();
        $person = factory(Person::class)->create();
        $user->persons()->attach($person->id);
        $photo = create($photo = Photo::class,['person_id' => $person->id]);
        $photoPathtokens = explode('/',$photo->path);
        $file = new File(base_path(self::AVATAR_PATH));
        Storage::disk('local')->putFileAs($photoPathtokens[0], $file , $photoPathtokens[1]);
        $this->signIn($user);
        $response = $this->get('/photos/' . $photo->id);
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'image/png');
    }

    /**
     * Logged users cannot see photos from other users.
     *
     * @test
     * @return void
     */
    public function logged_users_cannot_see_photos_from_other_users()
    {
        $user = factory(User::class)->create();
        $person = factory(Person::class)->create();
        $user->persons()->attach($person->id);
        $photo = create($photo = Photo::class,['person_id' => $person->id]);
        $photoPathtokens = explode('/',$photo->path);
        $file = new File(base_path(self::AVATAR_PATH));
        Storage::disk('local')->putFileAs($photoPathtokens[0], $file , $photoPathtokens[1]);

        $user2 = factory(User::class)->create();
        $person2 = factory(Person::class)->create();
        $user2->persons()->attach($person2->id);
        $photo2 = create($photo = Photo::class,['person_id' => $person2->id]);
        $photoPathtokens2 = explode('/',$photo2->path);
        $file2 = new File(base_path(self::AVATAR2_PATH));
        Storage::disk('local')->putFileAs($photoPathtokens2[0], $file2 , $photoPathtokens2[1]);


        $this->signIn($user);
        $response = $this->get('/photos/' . $photo2->id);
        $response->assertStatus(403);
    }

    /**
     * Manager user can see all photos.
     *
     * @test
     * @return void
     */
    public function manager_user_can_see_all_photos()
    {
        $user = factory(User::class)->create();
        $person = factory(Person::class)->create();
        $user->persons()->attach($person->id);
        $photo = create($photo = Photo::class,['person_id' => $person->id]);
        $photoPathtokens = explode('/',$photo->path);
        $file = new File(base_path(self::AVATAR2_PATH));
        Storage::disk('local')->putFileAs($photoPathtokens[0], $file , $photoPathtokens[1]);

        $this->signInAsRelationshipsManager();
        $response = $this->get('/photos/' . $photo->id);
        $response->assertStatus(200);
    }

    /**
     * Shows 404 for unexisting photo.
     *
     * @test
     * @return void
     */
    public function shows_404_for_unexisting_photo()
    {
        $this->signInAsRelationshipsManager();
        $response = $this->get('/photos/99999');
        $response->assertStatus(404);
    }


}
