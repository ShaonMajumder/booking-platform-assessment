<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\Service;
use Illuminate\Support\Facades\Log;
use Faker\Factory as Faker;

class SeedBookingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $faker;
    protected $chunk;
    protected static $cachedServices = null;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(int $chunk)
    {
        $this->chunk = $chunk;
        $this->faker = Faker::create();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            if (is_null(self::$cachedServices)) {
                self::$cachedServices = Service::pluck('id')->toArray();
            }

            // Instantiate the Faker object
            $faker = $this->faker;

            $bookings = [];

            for ($j = 0; $j < $this->chunk; $j++) {
                $bookings[] = [
                    'id' => (string) Str::uuid(),
                    'service_id' => $faker->randomElement(self::$cachedServices),
                    'name' => $faker->name(),
                    'phone_number' => $faker->numerify('+8801#########'),
                    'status' => BookingStatus::PENDING,
                    'schedule_date_time' => $faker->dateTimeBetween('+1 days', '+30 days'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            Booking::insert($bookings);

            Log::info("SeedBookingJob finished. Total inserted: " . count($bookings));
            
            unset($bookings);
            gc_collect_cycles();
        } catch (\Exception $e) {
            Log::error('Error in SeedBookingJob: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
            return;
        }
    }
}
