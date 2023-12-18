<?php

use App\Models\QuotesPortal\Product;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->json('last_price');
            $table->json('smallest_price');
            $table->integer('smallest_eta')->default(0);
        });

        Product::query()->each(function (Product $product) {
            $product->update([
                'last_price' => [
                    'currency' => 'BRL',
                    'amount' => 0,
                ],
                'smallest_price' => [
                    'currency' => 'BRL',
                    'amount' => 0,
                ],
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('last_price');
            $table->dropColumn('smallest_price');
            $table->dropColumn('smallest_eta');
        });
    }
};
