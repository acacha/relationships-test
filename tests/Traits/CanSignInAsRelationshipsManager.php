<?php

namespace Tests\Traits;

/**
 * Class CanSignInAsRelationshipsManager.
 *
 * @package Tests
 */
trait CanSignInAsRelationshipsManager
{
    use CreatesModels;

    /**
     * Sign in into application.
     *
     * @param null $user
     * @param null $driver
     * @return $this
     */
    protected function signIn($user = null, $driver = null)
    {
        $user = $user ?: $this->create('App\User');

        $this->actingAs($user, $driver);

        view()->share('signedIn',true);
        view()->share('user', $user);

        return $this;
    }

    /**
     * Sign in into application as relationships manager.
     *
     * @param null $driver
     * @return $this
     */
    protected function signInAsRelationshipsManager($driver = null)
    {
        return $this->signInWithRole('manage-relationships', $driver);
    }

    /**
     * Sign in with role.
     *
     * @param $role
     * @param null $driver
     * @return $this
     */
    protected function signInWithRole($role, $driver = null)
    {
        $user = $this->create('App\User');
        $user->assignRole($role);
        $this->signIn($user,$driver);
        return $this;
    }

}