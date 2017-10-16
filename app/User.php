<?php

namespace App;

use Acacha\Relationships\Models\Traits\HasPersons;
use Acacha\Relationships\Models\Traits\HasUserMigrationInfo;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

/**
 * Class User.
 *
 * @package App
 */
class User extends Authenticatable
{
    use Notifiable, HasUserMigrationInfo, HasRoles, HasApiTokens, HasPersons;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','initialPassword',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token','initialPassword',
    ];

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
//    protected $with = ['persons'];

}
