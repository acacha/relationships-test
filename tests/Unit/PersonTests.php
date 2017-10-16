<?php

namespace Tests\Unit;

use Acacha\Relationships\Models\Person;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Class PersonTests
 *
 * @package Tests\Unit
 */
class PersonTests extends TestCase
{
    use RefreshDatabase;

    /**
     * Photos are show in correct order.
     *
     * @test
     * @return void
     */
    public function photos_are_show_in_correct_order()
    {
        $this->assertTrue(true);
        create(User::class);
        add_photo_to_first_user();
        sleep(1);
        add_photo_to_first_user('node_modules/admin-lte/dist/img/avatar2.png');
        sleep(1);
        add_photo_to_first_user('node_modules/admin-lte/dist/img/avatar3.png');

        $person = Person::findOrFail(1);

        $this->assertCount(3, $person->photos);

        $this->assertTrue($person->photos()->first()->path === 'user_photos/avatar3.png');
    }
}
