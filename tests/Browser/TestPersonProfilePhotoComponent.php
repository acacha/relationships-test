<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

/**
 * Class TestUserProfilePhotoComponent.
 *
 * @package Tests\Browser
 */
class TestPersonProfilePhotoComponent extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * See upload photo component.
     *
     * @test
     * @return void
     */
    public function see_upload_photo_component()
    {
        dump(__FUNCTION__);

        $this->browse(function (Browser $browser) {
            $user = factory(\App\User::class)->create();
            view()->share('user', $user);
            $browser->loginAs($user)->visit('/test/component/user-profile-photo')
                ->assertSee('Upload photo')
                ->assertVisible("img[src^='/images/defaultmale.png']");
        });

        $this->logout();
    }

    /**
     * See upload photo component with female photo.
     *
     * @test
     * @return void
     */
    public function see_upload_photo_component_with_female_photo()
    {
        dump(__FUNCTION__);

        $this->browse(function (Browser $browser) {
            $user = factory(\App\User::class)->create();
            view()->share('user', $user);
            $browser->loginAs($user)->visit('/test/component/user-profile-photo?case=female')
                ->assertSee('Upload photo')
                ->assertVisible("img[src^='/images/defaultfemale.png']");
        });

        $this->logout();
    }

    /**
     * See correct photo for concrete user id.
     *
     * @test
     * @return void
     */
    public function see_correct_photo_for_concrete_user_id()
    {
        dump(__FUNCTION__);

        $this->browse(function (Browser $browser) {
            $user = factory(\App\User::class)->create();
            view()->share('user', $user);
            add_photo_to_first_user();
            $browser->loginAs($user)->visit('/test/component/user-profile-photo?case=with-user-id&user_id=1')
                ->assertSee('Upload photo')
                ->assertSee('Click to upload')
                ->assertVisible("img[src='/person/1/photo']");
        });

        $this->logout();
    }

    /**
     * See default photo for user without photo
     *
     * @test
     * @return void
     */
    public function see_default_photo_for_user_without_photo()
    {
        dump(__FUNCTION__);

        $this->browse(function (Browser $browser) {
            $user = factory(\App\User::class)->create();
            view()->share('user', $user);
            $browser->loginAs($user)->visit('/test/component/user-profile-photo?case=with-user-id&user_id=1')
                ->assertSee('Upload photo')
                ->assertSee('Click to upload')
                ->assertVisible("img[src^='/images/defaultmale.png']");
        });

        $this->logout();
    }

    /**
     * See correct photo for authenticated user with photo
     *
     * @group todo
     * @test
     * @return void
     */
    public function see_correct_photo_for_auth_user_with_photo()
    {
        dump(__FUNCTION__);

        $this->browse(function (Browser $browser) {
            $user = factory(\App\User::class)->create();
            view()->share('user', $user);
            add_photo_to_first_user();
            $browser->loginAs($user)->visit('/test/component/user-profile-photo')
                ->assertSee('Upload photo')
                ->assertSee('Click to upload')
                ->pause(9999999)
                ->assertVisible("img[src='/person/1/photo']");
        });

        $this->logout();
    }


    /**
     * Logout.
     */
    private function logout()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/home')
                ->click('#user_menu')
                ->click('#logout')
                ->pause(2000);
        });
    }
}
