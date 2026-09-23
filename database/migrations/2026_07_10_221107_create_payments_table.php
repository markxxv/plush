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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')
                ->constrained('orders')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->integer('amount');
            $table->string('currency', 3)->default('eur');
            $table->string('provider')->default('stripe');

            $table->string('provider_payment_id')->nullable();
            $table->string('provider_charge_id')->nullable();

            $table->string('status')->default('pending');
            $table->string('payment_method_type')->nullable();

            $table->json('payload')->nullable();
            $table->text('failure_reason')->nullable();

            $table->timestamp('paid_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
