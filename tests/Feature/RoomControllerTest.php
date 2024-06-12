<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Room;
use App\Models\User;

class RoomControllerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        // Create a user and get the token
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
    }

    /** @test */
    public function testCreateRoomWithProperData()
    {
        $response = $this->postJson('/api/rooms', [
            'number' => '101',
            'type' => 'Single',
            'price_per_night' => 100,
            'status' => 'available'
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'id', 'number', 'type', 'price_per_night', 'status', 'created_at', 'updated_at'
                 ]);

        $this->assertDatabaseHas('rooms', [
            'number' => '101',
            'type' => 'Single',
            'price_per_night' => 100,
            'status' => 'available'
        ]);
    }



    /** @test */
    public function testGetListOfRooms()
    {
        Room::factory()->count(3)->create();

        $response = $this->getJson('/api/rooms');

        $response->assertStatus(200)
                 ->assertJsonCount(3)
                 ->assertJsonStructure([
                     '*' => ['id', 'number', 'type', 'price_per_night', 'status', 'created_at', 'updated_at']
                 ]);
    }

    /** @test */
    public function testGetSingleRoom()
    {
        $room = Room::factory()->create();

        $response = $this->getJson("/api/rooms/{$room->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'id' => $room->id,
                     'number' => $room->number,
                     'type' => $room->type,
                     'price_per_night' => $room->price_per_night,
                     'status' => $room->status,
                 ]);
    }

    /** @test */
    public function testUpdateRoomWithProperData()
    {
        $room = Room::factory()->create();

        $response = $this->putJson("/api/rooms/{$room->id}", [
            'number' => '102',
            'type' => 'Double',
            'price_per_night' => 150,
            'status' => 'available'
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'id' => $room->id,
                     'number' => '102',
                     'type' => 'Double',
                     'price_per_night' => 150,
                     'status' => 'available',
                 ]);

        $this->assertDatabaseHas('rooms', [
            'id' => $room->id,
            'number' => '102',
            'type' => 'Double',
            'price_per_night' => 150,
            'status' => 'available'
        ]);
    }

    /** @test */
    public function testDeleteRoom()
    {
        $room = Room::factory()->create();

        $response = $this->deleteJson("/api/rooms/{$room->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('rooms', [
            'id' => $room->id
        ]);
    }
}
