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
        Schema::create('sim_ads', function (Blueprint $table) {
            $table->id();
            $table->string('owner_name');
            $table->string('number')->unique();
            $table->unsignedBigInteger('price_suggestion');
            $table->string('city');
            $table->enum('type', ['custom_offer', 'instant_sale']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sim_ads');
    }
};
