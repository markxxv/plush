<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->index(
                ['active', 'created_at'],
                'products_active_created_at_idx'
            );

            $table->index(
                ['active', 'id'],
                'products_active_id_idx'
            );
        });

        Schema::table('product_collections', function (Blueprint $table) {
            $table->index(
                ['active', 'position'],
                'product_collections_active_position_idx'
            );
        });

        Schema::table('media', function (Blueprint $table) {
            $table->index(
                ['model_type', 'model_id', 'collection_name', 'order_column'],
                'media_model_collection_order_idx'
            );
        });

        Schema::table('collection_product', function (Blueprint $table) {
            $table->index(
                ['product_collection_id', 'product_id'],
                'collection_product_collection_product_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::table('collection_product', function (Blueprint $table) {
            $table->dropIndex('collection_product_collection_product_idx');
        });

        Schema::table('media', function (Blueprint $table) {
            $table->dropIndex('media_model_collection_order_idx');
        });

        Schema::table('product_collections', function (Blueprint $table) {
            $table->dropIndex('product_collections_active_position_idx');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('products_active_created_at_idx');
            $table->dropIndex('products_active_id_idx');
        });
    }
};
