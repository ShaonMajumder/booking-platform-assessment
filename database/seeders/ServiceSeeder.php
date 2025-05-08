<?php

namespace Database\Seeders;

use App\Enums\ServiceCategory;
use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        foreach (ServiceCategory::values() as $category) {
            Service::create([
                'id' => (string) Str::uuid(),
                'name' => $category . ' Service',
                'category' => $category,
                'price' => 100,
                'description' => $category . ' description goes here.',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
