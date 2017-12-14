<?php

use App\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

if (!function_exists('assignPermission')) {
    function assignPermission($role, $permission) {
        if (! $role->hasPermissionTo($permission)) {
            $role->givePermissionTo($permission);
        }
    }
}

if (!function_exists('initialize_permissions')) {
    function initialize_permissions()
    {
        initialize_relationships_management_permissions();
    }
}

if (!function_exists('create_admin_user')) {
    function create_admin_user()
    {
        factory(User::class)->create([
            'name'     => env('ADMIN_USER_NAME', 'Sergi Tur Badenas'),
            'email'    => env('ADMIN_USER_EMAIL', 'sergiturbadenas@gmail.com'),
            'password' => bcrypt(env('ADMIN_USER_PASSWORD')),
        ]);
    }
}

if (!function_exists('first_user_as_manager')) {
    function first_user_as_manager()
    {
        $firstUser = User::all()->first();
        $firstUser->assignRole('manage-relationships');
        $firstUser->givePermissionTo('disable-validation');
    }
}
