<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            $table->string('title_en');
            $table->string('title_fr')->nullable();

            $table->boolean('active')->default(true);

            $table->string('slug_en')->unique();
            $table->string('slug_fr')->nullable()->unique();

            $table->longText('description_en')->nullable();
            $table->longText('description_fr')->nullable();

            $table->string('meta_title_en')->nullable();
            $table->string('meta_title_fr')->nullable();

            $table->text('meta_description_en')->nullable();
            $table->text('meta_description_fr')->nullable();

            $table->decimal('price', 10, 2)->default(0);

            $table->boolean('availability')->default(true);
            $table->boolean('preorder')->default(false);

            $table->timestamps();

            $table->index('availability');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};