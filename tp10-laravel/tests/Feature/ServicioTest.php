<?php

namespace Tests\Feature;

use Database\Seeders\ServicioSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Pruebas básicas del módulo de servicios (base SQLite en memoria).
 */
class ServicioTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ServicioSeeder::class);
    }

    public function test_listado_web_responde_200(): void
    {
        $this->get('/servicios')
            ->assertOk()
            ->assertSee('Frenos')
            ->assertSee('Diagnóstico');
    }

    public function test_inicio_redirige_al_listado(): void
    {
        $this->get('/')->assertRedirect(route('servicios.index'));
    }

    public function test_api_devuelve_json_con_los_servicios(): void
    {
        $this->getJson('/api/servicios')
            ->assertOk()
            ->assertHeader('Content-Type', 'application/json')
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('total', 4)
            ->assertJsonStructure(['data' => [['id', 'nombre', 'descripcion', 'precio_base', 'turnos']]]);
    }

    public function test_api_devuelve_404_si_no_existe(): void
    {
        $this->getJson('/api/servicios/999')
            ->assertNotFound()
            ->assertJsonPath('status', 'error');
    }

    public function test_alta_valida_y_guarda(): void
    {
        $this->post('/servicios', ['nombre' => '', 'precio_base' => '-1'])
            ->assertSessionHasErrors(['nombre', 'precio_base']);

        $this->post('/servicios', ['nombre' => 'Alineación', 'descripcion' => 'Alineación y balanceo.', 'precio_base' => '30.000,50'])
            ->assertRedirect(route('servicios.index'));

        $this->assertDatabaseHas('servicios', ['nombre' => 'Alineación', 'precio_base' => 30000.50]);
    }

    public function test_no_borra_un_servicio_con_turnos(): void
    {
        $id = \App\Models\Servicio::where('nombre', 'Service')->value('id');

        $this->delete("/servicios/{$id}")->assertSessionHas('error');
        $this->assertDatabaseHas('servicios', ['id' => $id]);
    }
}
