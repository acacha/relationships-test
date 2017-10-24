<?php

namespace Tests\Feature;

use Acacha\Relationships\Models\Person;
use Acacha\Relationships\Models\Photo;
use App;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\File;
use Illuminate\Http\UploadedFile;
use Storage;
use Tests\TestCase;
use Tests\Traits\CanSignInAsRelationshipsManager;
use Tests\Traits\CheckJsonAPIUriAuthorization;

/**
 * Class PeopleControllerTest.
 *
 * Please run npm install before executing this tests
 *
 * @package Tests\Feature
 */
class PeopleControllerTest extends TestCase
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
     * .
     *
     * @return void
     */
    public function todo()
    {
        $url = '';
        $this->json('POST',$url);
//        $user = factory(User::class)->create();
//        $person = factory(Person::class)->create();
//        $user->persons()->attach($person->id);
//        $photo = create($photo = Photo::class,['person_id' => $person->id]);
//        $photoPathtokens = explode('/',$photo->path);
//        $file = new File(base_path(self::AVATAR_PATH));
//        Storage::disk('local')->putFileAs($photoPathtokens[0], $file , $photoPathtokens[1]);
//        $this->signIn($user);
//        $response = $this->get('/photos/' . $photo->id);
//        $response->assertStatus(200);
//        $response->assertHeader('Content-Type', 'image/png');
    }


}
