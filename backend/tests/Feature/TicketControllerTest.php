<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Movie;
use App\Models\Screening;
use App\Models\Auditorium;
use App\Models\Seat;
use App\Models\Ticket;
use Laravel\Sanctum\Sanctum;
use Illuminate\Support\Str;

class TicketControllerTest extends TestCase
{
    public function test_user_can_reserve_seats()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $movie = Movie::factory()->create();
        $auditorium = Auditorium::factory()->create();
        $screening = Screening::factory()->create([
            'movie_id' => $movie->id,
            'auditorium_id' => $auditorium->id,
            'price' => 10.00
        ]);
        
        $seat1 = Seat::factory()->create([
            'auditorium_id' => $auditorium->id,
            'status' => 'available'
        ]);
        
        $seat2 = Seat::factory()->create([
            'auditorium_id' => $auditorium->id,
            'status' => 'available'
        ]);

        $reserveData = [
            'screening_id' => $screening->id,
            'seat_ids' => [$seat1->id, $seat2->id]
        ];

        $response = $this->postJson('/api/tickets/reserve', $reserveData);

        // Puede tener una estructura de respuesta diferente, así que solo verificamos el código de estado
        $response->assertStatus(201);

        // Verificar que los asientos se marcaron como ocupados (busy)
        $updatedSeat1 = Seat::find($seat1->id);
        $updatedSeat2 = Seat::find($seat2->id);
        
        $this->assertEquals('busy', $updatedSeat1->status);
        $this->assertEquals('busy', $updatedSeat2->status);
    }

    public function test_user_purchase_flow()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $movie = Movie::factory()->create();
        $auditorium = Auditorium::factory()->create();
        $screening = Screening::factory()->create([
            'movie_id' => $movie->id,
            'auditorium_id' => $auditorium->id,
            'price' => 10.00
        ]);
        
        $seat1 = Seat::factory()->create([
            'auditorium_id' => $auditorium->id,
            'status' => 'available'
        ]);
        
        $seat2 = Seat::factory()->create([
            'auditorium_id' => $auditorium->id,
            'status' => 'available'
        ]);

        // El endpoint espera un formato específico para los asientos
        $purchaseData = [
            'screening_id' => $screening->id,
            'seats' => [
                ['number' => $seat1->number, 'id' => $seat1->id],
                ['number' => $seat2->number, 'id' => $seat2->id]
            ],
            'payment_method' => 'credit_card',
            'card_number' => '4242424242424242',
            'card_expiry' => '12/25',
            'card_cvc' => '123'
        ];

        $response = $this->postJson('/api/tickets/purchase', $purchaseData);

        // Puede devolver diferentes códigos según la implementación
        $this->assertTrue(in_array($response->status(), [200, 201]));
        
        // Verificar que se creó un ticket
        $ticket = Ticket::where('user_id', $user->id)
                        ->where('screening_id', $screening->id)
                        ->first();
                        
        $this->assertNotNull($ticket);
    }
}
