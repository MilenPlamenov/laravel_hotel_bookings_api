<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Booking;
use App\Models\Room;
use App\Models\User;

class BookingControllerTest extends TestCase
{
    use RefreshDatabase;

    // add test which checks if the room is already booked !


    public function setUp(): void
    {
        parent::setUp();
        // Create a user and get the token
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
    }

    /** @test */
    public function testCreateBookingWithProperData()
    {
        $room = Room::factory()->create();
        $user_id = User::factory()->create();

        $response = $this->postJson('/api/bookings', [
            'room_id' => $room->id,
            'user_id' => $user_id->id,
            'check_in_date' => '2024-06-15',
            'check_out_date' => '2024-06-20',
            'total_price' => 500
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'id', 'room_id', 'user_id', 'check_in_date', 'check_out_date', 'total_price', 'created_at', 'updated_at'
                 ]);

        $this->assertDatabaseHas('bookings', [
            'room_id' => $room->id,
            'user_id' => $user_id->id,
            'check_in_date' => '2024-06-15',
            'check_out_date' => '2024-06-20',
            'total_price' => 500
        ]);
    }

    /** @test */
    public function testGetAllBookings()
    {
        Booking::factory()->count(3)->create();

        $response = $this->getJson('/api/bookings');

        $response->assertStatus(200)
                 ->assertJsonCount(3)
                 ->assertJsonStructure([
                     '*' => ['id', 'room_id', 'user_id', 'check_in_date', 'check_out_date', 'total_price', 'created_at', 'updated_at']
                 ]);
    }

    /** @test */
    public function testGetSingleBooking()
    {
        $booking = Booking::factory()->create();

        $response = $this->getJson("/api/bookings/{$booking->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'id' => $booking->id,
                     'room_id' => $booking->room_id,
                     'user_id' => $booking->user_id,
                     'check_in_date' => $booking->check_in_date->format('Y-m-d'),
                     'check_out_date' => $booking->check_out_date->format('Y-m-d'),
                     'total_price' => $booking->total_price,
                 ]);
    }


    /** @test */
    public function testUpdateBookingWithProperData()
    {
        $booking = Booking::factory()->create();

        $response = $this->putJson("/api/bookings/{$booking->id}", [
            'room_id' => $booking->room_id,
            'user_id' => $booking->user_id,
            'check_in_date' => '2024-06-16',
            'check_out_date' => '2024-06-21',
            'total_price' => 600
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'id' => $booking->id,
                     'room_id' => $booking->room_id,
                     'user_id' => $booking->user_id,
                     'check_in_date' => '2024-06-16',
                     'check_out_date' => '2024-06-21',
                     'total_price' => '600.00',
                 ]);

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'room_id' => $booking->room_id,
            'user_id' => $booking->user_id,
            'check_in_date' => '2024-06-16',
            'check_out_date' => '2024-06-21',
            'total_price' => 600
        ]);
    }


    /** @test */
    public function testDeleteBooking()
    {
        $booking = Booking::factory()->create();

        $response = $this->deleteJson("/api/bookings/{$booking->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('bookings', [
            'id' => $booking->id
        ]);
    }


    /** @test */
    public function testCreateBookingWithOverlappingDate()
    {
        $room = Room::factory()->create();
        $user = User::factory()->create();

        // Create an existing booking
        Booking::factory()->create([
            'room_id' => $room->id,
            'check_in_date' => '2024-06-15',
            'check_out_date' => '2024-06-20',
        ]);

        // Try to create a new booking with overlapping dates
        $response = $this->postJson('/api/bookings', [
            'room_id' => $room->id,
            'user_id' => $user->id,
            'check_in_date' => '2024-06-18',
            'check_out_date' => '2024-06-22',
            'total_price' => 500
        ]);

        $response->assertStatus(400)
                 ->assertJson([
                     'error' => 'Room is not available for the selected dates'
                 ]);
    }

}
