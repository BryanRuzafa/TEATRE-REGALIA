<?php

namespace Tests\Feature;

use App\Models\Esdeveniment;
use App\Models\Seient;
use App\Models\Venda;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * Proves del bloqueig de butaques: dues persones no poden reservar
 * ni comprar la mateixa butaca.
 */
class ReservaSeientsTest extends TestCase
{
    use RefreshDatabase;

    private Esdeveniment $esdeveniment;

    protected function setUp(): void
    {
        parent::setUp();

        $this->esdeveniment = Esdeveniment::create([
            'titol' => 'La Traviata',
            'data_hora' => now()->addWeek(),
            'recinte' => 'Sala Gran',
            'preu_base' => 35.50,
        ]);
    }

    private function crearSeient(string $referencia, array $atributs = []): Seient
    {
        return Seient::create(array_merge([
            'esdeveniment_id' => $this->esdeveniment->id,
            'id_referencia' => $referencia,
            'estat' => 'disponible',
        ], $atributs));
    }

    private function reservar(string $referencia, string $client): \Illuminate\Testing\TestResponse
    {
        return $this->postJson('/api/seients/reservar', [
            'id_seient' => $referencia,
            'esdeveniment_id' => $this->esdeveniment->id,
            'client_id' => $client,
        ]);
    }

    public function test_es_pot_reservar_una_butaca_disponible(): void
    {
        $this->crearSeient('A-1');

        $this->reservar('A-1', 'client-1')
            ->assertOk()
            ->assertJson(['exit' => true]);

        $seient = Seient::where('id_referencia', 'A-1')->first();
        $this->assertSame('reservat', $seient->estat);
        $this->assertSame('client-1', $seient->reservat_per);
        $this->assertNotNull($seient->temps_bloqueig_finalitza);
    }

    public function test_reservar_una_butaca_ja_reservada_retorna_409_i_no_canvia_el_propietari(): void
    {
        $this->crearSeient('A-2');

        $this->reservar('A-2', 'client-1')->assertOk();
        $this->reservar('A-2', 'client-2')->assertStatus(409);

        $this->assertSame('client-1', Seient::where('id_referencia', 'A-2')->value('reservat_per'));
    }

    public function test_no_es_pot_reservar_una_butaca_venuda(): void
    {
        $this->crearSeient('A-3', ['estat' => 'venut']);

        $this->reservar('A-3', 'client-1')->assertStatus(409);
    }

    public function test_alliberar_una_reserva_permet_que_un_altre_client_la_reservi(): void
    {
        $this->crearSeient('A-4');
        $this->reservar('A-4', 'client-1')->assertOk();

        $this->postJson('/api/seients/alliberar', [
            'id_seient' => 'A-4',
            'esdeveniment_id' => $this->esdeveniment->id,
        ])->assertOk();

        $this->reservar('A-4', 'client-2')->assertOk();
        $this->assertSame('client-2', Seient::where('id_referencia', 'A-4')->value('reservat_per'));
    }

    public function test_el_client_que_te_la_reserva_pot_comprar_la_butaca(): void
    {
        Mail::fake();
        $this->crearSeient('B-1');
        $this->reservar('B-1', 'client-1')->assertOk();

        $this->postJson('/api/venda/confirmar', [
            'esdeveniment_id' => $this->esdeveniment->id,
            'client_id' => 'client-1',
            'nom' => 'Anna Puig',
            'email' => 'anna@example.com',
            'seients' => ['B-1'],
        ])->assertOk()->assertJson(['exit' => true]);

        $seient = Seient::where('id_referencia', 'B-1')->first();
        $this->assertSame('venut', $seient->estat);
        $this->assertNull($seient->reservat_per);
        $this->assertDatabaseHas('vendes', [
            'seient_id' => $seient->id,
            'email_comprador' => 'anna@example.com',
        ]);
    }

    public function test_no_es_pot_comprar_una_butaca_reservada_per_un_altre_client(): void
    {
        Mail::fake();
        $this->crearSeient('B-2');
        $this->reservar('B-2', 'client-1')->assertOk();

        $this->postJson('/api/venda/confirmar', [
            'esdeveniment_id' => $this->esdeveniment->id,
            'client_id' => 'client-2',
            'nom' => 'Pau Serra',
            'email' => 'pau@example.com',
            'seients' => ['B-2'],
        ])->assertStatus(400)->assertJson(['exit' => false]);

        $this->assertSame('reservat', Seient::where('id_referencia', 'B-2')->value('estat'));
        $this->assertSame(0, Venda::count());
    }
}
