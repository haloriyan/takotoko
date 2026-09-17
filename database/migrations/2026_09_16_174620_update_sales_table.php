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
        Schema::table('sales', function (Blueprint $table) {
            $table->bigInteger('fee')->after('total_margin');
            $table->bigInteger('total_pay')->after('fee');
            $table->string('payment_method')->after('total_pay');
            $table->string('payment_status')->after('payment_method');
            $table->text('payment_payload')->nullable()->after('payment_status');
            $table->boolean('has_payout')->after('payment_payload');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
