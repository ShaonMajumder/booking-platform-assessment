<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Carbon\Carbon;

class BookingTest extends TestCase
{
    public function test_booking_creation()
    {
        $service = Service::factory()->create();

        $payload = [
            'service_id' => $service->id,
            'name' => 'John Doe',
            'phone' => '01712345678',
            'schedule_date' => Carbon::now()->addDays(2)->toDateString(),
        ];

        $response = $this->postJson('/api/bookings', $payload);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'booking_id',
                     'status',
                 ])
                 ->assertJson(['status' => 'pending']);
    }

    public function test_booking_status_retrieval()
    {
        $booking = Booking::factory()->create();

        $response = $this->getJson("/api/bookings/{$booking->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'booking_id' => $booking->id,
                     'status' => 'pending', // assuming initial status is 0 = pending
                 ]);
    }
}
