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
        Schema::create('product_collections', function (Blueprint $table) {
            $table->id();

            $table->string('slug_en')->unique();
            $table->string('slug_fr')->nullable()->unique();

             $table->boolean('active')->default(true);

            $table->string('name_en');
            $table->string('name_fr')->nullable();

            $table->longText('description_en')->nullable();
            $table->longText('description_fr')->nullable();

            $table->string('video')->nullable();

            $table->string('meta_title_en')->nullable();
            $table->string('meta_title_fr')->nullable();

            $table->text('meta_description_en')->nullable();
            $table->text('meta_description_fr')->nullable();

            $table->json('path_en')->nullable();
            $table->json('path_fr')->nullable();

            $table->unsignedInteger('position')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_collections');
    }
};
