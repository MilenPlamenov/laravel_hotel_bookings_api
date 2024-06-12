<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Payment;
use App\Models\Booking;
use App\Models\User;
use App\Models\Room;

class PaymentControllerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
    }

    /** @test */
    public function testCreatePaymentWithProperData()
    {
        $booking = Booking::factory()->create();

        $response = $this->postJson('/api/payments', [
            'booking_id' => $booking->id,
            'amount' => 200,
            'payment_date' => '2024-06-10',
            'status' => 'pending'
        ]);

        $response->assertStatus(201)
                 ->assertJson([
                     'booking_id' => $booking->id,
                     'amount' => 200,
                     'payment_date' => '2024-06-10',
                     'status' => 'pending'
                 ]);

        $this->assertDatabaseHas('payments', [
            'booking_id' => $booking->id,
            'amount' => 200,
            'payment_date' => '2024-06-10',
            'status' => 'pending'
        ]);
    }

    /** @test */
    public function testUpdatePaymentWithProperData()
    {
        $payment = Payment::factory()->create();

        $response = $this->putJson("/api/payments/{$payment->id}", [
            'amount' => 250,
            'payment_date' => '2024-06-12',
            'status' => 'completed'
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'id' => $payment->id,
                     'amount' => 250,
                     'payment_date' => '2024-06-12',
                     'status' => 'completed'
                 ]);

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'amount' => 250,
            'payment_date' => '2024-06-12',
            'status' => 'completed'
        ]);
    }

    /** @test */
    public function testDeletePayment()
    {
        $payment = Payment::factory()->create();

        $response = $this->deleteJson("/api/payments/{$payment->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('payments', [
            'id' => $payment->id
        ]);
    }

    /** @test */
    public function testCreatePaymentWithIncorrectStatus()
    {
        $booking = Booking::factory()->create();

        $response = $this->postJson('/api/payments', [
            'booking_id' => $booking->id,
            'amount' => 200,
            'payment_date' => '2024-06-10',
            'status' => 'invalid_status'
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['status']);
    }
}
