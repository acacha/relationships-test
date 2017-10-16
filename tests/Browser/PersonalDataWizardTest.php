<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class PersonalDataWizardTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     * TODO
     * @return void
     */
    public function testExample()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/wizard')
                    ->assertSee('Laravel');
        });
    }
}
