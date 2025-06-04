<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->string('tracking_number')->unique();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->string('status')->default('pending');
            $table->string('carrier');
            $table->string('service_type');
            $table->decimal('weight', 8, 2);
            $table->string('weight_unit')->default('kg');
            $table->json('origin_address');
            $table->json('destination_address');
            $table->json('package_details');
            $table->decimal('shipping_cost', 10, 2);
            $table->string('currency')->default('USD');
            $table->string('label_url')->nullable();
            $table->timestamp('estimated_delivery')->nullable();
            $table->timestamp('actual_delivery')->nullable();
            $table->json('tracking_history')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shipments');
    }
};
