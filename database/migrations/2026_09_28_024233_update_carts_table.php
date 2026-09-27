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
        Schema::table('carts', function (Blueprint $table) {
            $table->boolean('is_composition')->after('notes');
            $table->bigInteger('cart_parent_id')->unsigned()->index()->nullable()->after('is_composition');
            $table->foreign('cart_parent_id')->references('id')->on('carts')->onDelete('cascade');
            $table->bigInteger('composition_id')->unsigned()->index()->nullable()->after('cart_parent_id');
            $table->foreign('composition_id')->references('id')->on('product_compositions')->onDelete('cascade');
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
