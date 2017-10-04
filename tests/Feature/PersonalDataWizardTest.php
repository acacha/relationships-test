<?php

namespace Tests\Feature;

use App;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\CanSignInAsRelationshipsManager;
use Tests\Traits\ChecksURIsAuthorization;

/**
 * Class PersonalDataWizardTest.
 *
 * @package Tests\Feature
 */
class PersonalDataWizardTest extends TestCase
{
    use RefreshDatabase, CanSignInAsRelationshipsManager, ChecksURIsAuthorization;

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
     * Test authorization for URI /wizard.
     *
     * @test
     * @return void
     */
    public function check_authorization_uri_relationships_wizard()
    {
        $this->check_authorization_uri('/wizard');
    }
}
