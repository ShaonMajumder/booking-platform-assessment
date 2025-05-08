<?php

namespace Database\Factories;

use App\Enums\BookingStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Service;

class BookingFactory extends Factory
{
    protected static $cachedServices = null;
    
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        if (is_null(self::$cachedServices)) {
            self::$cachedServices = Service::pluck('id')->toArray();
        }

        return [
            'id' => (string) Str::uuid(),
            'name' => $this->faker->name(),
            'phone_number' => $this->faker->numerify('+8801#########'),
            'service_id' => $this->faker->randomElement(self::$cachedServices),
            'status' => BookingStatus::PENDING, //$this->faker->randomElement(BookingStatus::all()),
            'schedule_date_time' => $this->faker->dateTimeBetween('+1 days', '+30 days'),
            // 'created_at' => now(),
            // 'updated_at' => now(),
        ];
    }
}
