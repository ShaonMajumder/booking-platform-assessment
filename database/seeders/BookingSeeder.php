<?php

namespace Database\Seeders;

use App\Jobs\SeedBookingJob;
use App\Models\Booking;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $total = 1000000; // 1M bookings
        $chunk = 5000;

        $recordsInserted = 0;

        for ($i = 0; $i < $total / $chunk; $i++) {
            SeedBookingJob::dispatch($chunk);

            $recordsInserted += $chunk;

            if ($i % 10 == 0) {
                $this->command->info("Dispatched {$recordsInserted} bookings...");
            }
        }

        $this->command->info("Successfully dispatched {$recordsInserted} booking jobs.");
    }
}
