<?php

namespace Tests\Unit;

use Acacha\Relationships\Models\Person;
use App\User;
use File;
use Storage;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Class HelpersTest
 *
 * @package Tests\Unit
 */
class HelpersTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Add photo to user.
     * @test
     * @return void
     */
    public function add_photo_to_user()
    {
        $path = 'node_modules/admin-lte/dist/img/avatar.png';
        $user = create(User::class);

        add_photo_to_user($user, $path);
        $photo = $user->person->photos()->first();

        $this->assertEquals('avatar.png',$photo->origin);
        $this->assertEquals('local',$photo->storage);
        $suffix = $user->person->id . '-' . snake_case($user->person->name) . '.png';
        $this->assertStringEndsWith($suffix,$photo->path);

        $this->assertTrue(Storage::disk('local')->exists($photo->path));
    }

    /**
     * Add photo to person.
     *
     * @test
     * @return void
     */
    public function add_photo_to_person()
    {
        $path = 'node_modules/admin-lte/dist/img/avatar.png';
        $person = create(Person::class);

        add_photo_to_person($path, $person);
        $photo = $person->photos()->first();

        $this->assertEquals('avatar.png',$photo->origin);
        $this->assertEquals('local',$photo->storage);
        $suffix = $person->id . '-' . snake_case($person->name) . '.png';
        $this->assertStringEndsWith($suffix,$photo->path);

        $this->assertTrue(Storage::disk('local')->exists($photo->path));

    }

    /**
     * Create person with photo.
     *
     * @test
     * @return void
     */
    public function create_person_with_photo()
    {
        $person = create_person_with_photo();
        $photo = $person->photos()->first();

        $this->assertEquals('local',$photo->storage);

        $person = create_person_with_photo('node_modules/admin-lte/dist/img/avatar.png');

        $photo = $person->photos()->first();

        $this->assertEquals('avatar.png',$photo->origin);
        $this->assertEquals('local',$photo->storage);
        $suffix = $person->id . '-' . snake_case($person->name) . '.png';
        $this->assertStringEndsWith($suffix,$photo->path);

    }
}
