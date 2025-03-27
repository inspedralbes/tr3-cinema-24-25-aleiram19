<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Seat;
use App\Models\Auditorium;
use Laravel\Sanctum\Sanctum;

class SeatControllerTest extends TestCase
{
    public function test_can_get_all_seats()
    {
        $auditorium = Auditorium::factory()->create();
        $seats = Seat::factory()->count(10)->create([
            'auditorium_id' => $auditorium->id
        ]);

        $response = $this->getJson('/api/seats');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     '*' => [
                         'id',
                         'number',
                         'auditorium_id',
                         'status'
                     ]
                 ]);
    }

    public function test_can_update_seat_status()
    {
        $auditorium = Auditorium::factory()->create();
        $seat = Seat::factory()->create([
            'auditorium_id' => $auditorium->id,
            'status' => 'available'
        ]);

        $updatedData = [
            'status' => 'busy'
        ];

        $response = $this->putJson("/api/seats/{$seat->id}/status", $updatedData);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'message',
                     'seat' => [
                         'id',
                         'status'
                     ]
                 ]);

        $this->assertDatabaseHas('seats', [
            'id' => $seat->id,
            'status' => 'busy'
        ]);
    }

    public function test_can_reset_seats()
    {
        $auditorium = Auditorium::factory()->create();
        $seats = Seat::factory()->count(5)->create([
            'auditorium_id' => $auditorium->id,
            'status' => 'busy'
        ]);

        $response = $this->postJson('/api/seats/reset', [
            'auditorium_id' => $auditorium->id
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'message'
                 ]);

        // Verificar que todos los asientos están disponibles
        $this->assertEquals(
            5, 
            Seat::where('auditorium_id', $auditorium->id)
                ->where('status', 'available')
                ->count()
        );
    }

    public function test_invalid_status_returns_error()
    {
        $auditorium = Auditorium::factory()->create();
        $seat = Seat::factory()->create([
            'auditorium_id' => $auditorium->id
        ]);

        $updatedData = [
            'status' => 'invalid_status'
        ];

        $response = $this->putJson("/api/seats/{$seat->id}/status", $updatedData);

        // Puede devolver una respuesta 422, 400 o similar
        $this->assertTrue($response->status() >= 400 && $response->status() < 500);
    }
}
