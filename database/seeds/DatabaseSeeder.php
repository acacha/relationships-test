<?php

use App\User;
use Illuminate\Database\Seeder;

/**
 * Class DatabaseSeeder.
 */
class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
        seed_identifier_types();

        create_admin_user();

        initialize_permissions();

        first_user_as_manager();

        factory(User::class,50)->create();
        $this->call(LocationsTableSeeder::class);
    }
}
