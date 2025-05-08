<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('service_id');
            $table->string('name');
            $table->string('phone_number');
            $table->unsignedTinyInteger('status')->default(0);
            $table->dateTime('schedule_date_time')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexing for performance
            $table->index('service_id');
            $table->index('schedule_date_time');
            $table->index('phone_number');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bookings');
    }
}
