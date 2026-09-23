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
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('title_en');
            $table->string('title_fr')->nullable();;
            $table->text('body_en')->nullable();
            $table->text('body_fr')->nullable();
            $table->text('content')->nullable();
            $table->boolean('active')->default(1);
            $table->string('language')->default('en');
            $table->string('cover')->nullable();
            $table->string('url_en')->unique();
            $table->string('url_fr')->unique();
            $table->string('meta_title_en')->nullable();
            $table->string('meta_description_en')->nullable();
            $table->string('meta_title_fr')->nullable();
            $table->string('meta_description_fr')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
