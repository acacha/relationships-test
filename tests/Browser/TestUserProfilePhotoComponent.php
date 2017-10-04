<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

/**
 * Class TestUserProfilePhotoComponent.
 *
 * @package Tests\Browser
 */
class TestUserProfilePhotoComponent extends DuskTestCase
{
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
                ->assertSee('Upload photo');
        });

        $this->logout();
    }

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
                ->assertSee('Upload photo');
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
