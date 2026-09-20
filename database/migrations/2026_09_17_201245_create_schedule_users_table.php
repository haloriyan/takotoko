<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('schedule_users', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('store_id')->unsigned()->index();
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
            $table->bigInteger('schedule_id')->unsigned()->index();
            $table->foreign('schedule_id')->references('id')->on('schedules')->onDelete('cascade');
            $table->bigInteger('user_id')->unsigned()->index();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->date('date');

            $table->dateTime('check_in_at')->nullable();
            $table->string('check_in_latitude')->nullable();
            $table->string('check_in_longitude')->nullable();
            $table->string('check_in_address')->nullable();
            $table->string('check_in_image')->nullable();

            $table->dateTime('check_out_at')->nullable();
            $table->string('check_out_latitude')->nullable();
            $table->string('check_out_longitude')->nullable();
            $table->string('check_out_address')->nullable();
            $table->string('check_out_image')->nullable();

            $table->integer('check_in_diff')->nullable(); // In Minutes
            $table->integer('check_out_diff')->nullable();
            $table->integer('in_out_diff')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedule_users');
    }
};
