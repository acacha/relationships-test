<?php

namespace Tests\Feature;

use Acacha\Relationships\Models\Person;
use App;
use App\User;
use Auth;
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
     *  An user can obtain is own user info.
     *
     * @test
     * @return void
     */
    public function an_user_can_obtain_is_own_user_info()
    {
        $user = factory(User::class)->create();
        $this->signIn($user,'api');
        $person = factory(Person::class)->create();
        $user->persons()->attach($person);
        $response = $this->json('GET','/api/v1/relationships/user/' . $user->id);

        $response->assertStatus(200);
        $response->assertJson([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'persons' => [
                0 => [
                    'id' => $person->id,
                    'name' => $person->name
                ]
            ]
        ]);
    }
    /**
     *  An user can obtain is own user info withour user id.
     *
     * @test
     * @return void
     */
    public function an_user_can_obtain_is_own_user_info_without_user_id()
    {
        $user = factory(User::class)->create();
        $this->signIn($user,'api');
        $person = factory(Person::class)->create();
        $user->persons()->attach($person);
        $response = $this->json('GET','/api/v1/relationships/user');
        $response->assertStatus(200);
        $response->assertJson([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'persons' => [
                0 => [
                    'id' => $person->id,
                    'name' => $person->name
                ]
            ]
        ]);
    }

    /**
     *  An user with permissions can obtain user info from another user.
     *
     * @test
     * @return void
     */
    public function an_user_with_permissions_can_obtain_user_info_from_another_user()
    {
        $this->signInAsRelationshipsManager('api');
        $user = factory(User::class)->create();
        $person = factory(Person::class)->create();
        $user->persons()->attach($person);
        $response = $this->json('GET','/api/v1/relationships/user/' . $user->id);

        $response->assertStatus(200);
        $response->assertJson([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'persons' => [
                0 => [
                    'id' => $person->id,
                    'name' => $person->name
                ]
            ]
        ]);
    }

    /**
     * Check returns 404 for non existing user id.
     *
     * @test
     * @return void
     */
    public function check_returns_404_for_non_existing_id()
    {
        $this->signInAsRelationshipsManager('api');
        $response = $this->json('GET','/api/v1/relationships/user/999');
        $response->assertStatus(404);
    }

    /**
     * Test authorization for URI /api/v1/relationships/user.
     *
     * @test
     * @return void
     */
    public function check_authorization_uri_to_obtain_user_info_for_logged_user()
    {
        $this->json('GET','/api/v1/relationships/user')->assertStatus(401);
        // This URL is public all users can obtain is own user info
        $this->signIn($user = create(User::class),'api');
        $this->json('GET','/api/v1/relationships/user')->assertStatus(200);
        $this->json('GET','/api/v1/relationships/user/'. $user->id)->assertStatus(200);
    }

    /**
     * Test authorization for URI /api/v1/relationships/user/{user}.
     *
     * @test
     * @return void
     */
    public function check_authorization_uri_to_obtain_user_info_for_an_specific_user()
    {
        $user = create('App\User');
        $this->check_json_api_uri_authorization("/api/v1/relationships/user/" . $user->id,'get');
    }

}
