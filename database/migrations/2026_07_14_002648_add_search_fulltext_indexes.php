<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->fullText(
                [
                    'title_en',
                    'title_fr',
                    'description_en',
                    'description_fr',
                ],
                'products_search_fulltext'
            );
        });

        Schema::table('product_collections', function (Blueprint $table) {
            $table->fullText(
                [
                    'name_en',
                    'name_fr',
                    'description_en',
                    'description_fr',
                ],
                'collections_search_fulltext'
            );
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropFullText('products_search_fulltext');
        });

        Schema::table('product_collections', function (Blueprint $table) {
            $table->dropFullText('collections_search_fulltext');
        });
    }
};
