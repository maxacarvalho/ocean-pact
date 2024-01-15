<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('predicted_purchase_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->string('quote_number')->index();
            $table->unsignedBigInteger('quote_id');
            $table->unsignedBigInteger('supplier_id');
            $table->unsignedBigInteger('product_id');
            $table->string('item')->index();
            $table->unsignedBigInteger('quote_item_id');
            $table->date('delivery_date');
            $table->json('price')->nullable();
            $table->json('last_price')->nullable();
            $table->date('necessity_date');
            $table->timestamps();

            $table->foreign('company_id')
                ->references('id')
                ->on('companies');
            $table->foreign('quote_id')
                ->references('id')
                ->on('quotes');
            $table->foreign('supplier_id')
                ->references('id')
                ->on('suppliers');
            $table->foreign('quote_item_id')
                ->references('id')
                ->on('quote_items');
            $table->foreign('product_id')
                ->references('id')
                ->on('products');

            $table->unique(['quote_number', 'item']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('predicted_purchase_requests');
    }
};
