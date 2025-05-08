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

        $response = $this->postJson('/api/v1/bookings', $payload);

        $response->assertStatus(201)
         ->assertJsonStructure([
             'success',
             'message',
             'data' => [
                 'booking_id',
                 'name',
                 'phone_number',
                 'service_id',
                 'booking_date',
                 'status',
             ],
         ])
         ->assertJson([
             'success' => true,
             'message' => 'Booking created successfully.',
             'data' => [
                 'status' => 'pending',
             ],
         ]);
              

    }

    public function test_booking_status_retrieval()
    {
        $booking = Booking::factory()->create();

        $response = $this->getJson("/api/v1/bookings/{$booking->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'booking_id',
                    'status',
                    'service' => [
                        'id',
                        'name',
                        'category',
                        'price',
                        'description',
                    ],
                    'schedule_date_time',
                ],
            ]);

    }
}
