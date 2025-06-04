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
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('from_address_id')->constrained('shipping_addresses');
            $table->foreignId('to_address_id')->constrained('shipping_addresses');
            $table->foreignId('rate_id')->constrained('shipping_rates');
            $table->string('tracking_number')->unique();
            $table->string('status');
            $table->decimal('weight', 10, 2);
            $table->string('weight_unit', 2)->default('lb');
            $table->json('dimensions')->nullable();
            $table->json('customs_info')->nullable();
            $table->json('insurance')->nullable();
            $table->decimal('total_price', 10, 2);
            $table->string('currency', 3)->default('USD');
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
