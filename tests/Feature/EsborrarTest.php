<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Class EsborrarTest.
 *
 * @package Tests\Feature
 */
class EsborrarTest extends TestCase
{
    /**
     * @test
     */
    public function una_connexio_ha_de_mostrar_tarifa()
    {
        $tarifa = factory(Tarifa::class)->create();
        $connexio = factory(Connexio::class)->create([
            'tarifa_id' => $tarifa->id
        ]);
        $response = $this->get('connexio/' . $connexio->id);
        $response->assertSuccessful();

        $response->assertSeeText($tarifa->name);
    }

}
