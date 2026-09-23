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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->string('order_number')->unique();
            $table->string('name');
            $table->string('secondname')->nullable();
            $table->string('phone');
            $table->string('email')->nullable();

            $table->string('country')->nullable();
            $table->string('delivery_country_code')->nullable();
            $table->string('zip')->nullable();
            $table->string('city')->nullable();

            $table->text('message')->nullable();
            $table->text('address');

            $table->json('items');

            $table->decimal('total_amount', 10, 2);
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('delivery_cost', 10, 2)->default(0);

            $table->string('status')->default('pending')->index();

            $table->timestamps();

            $table->index('order_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
