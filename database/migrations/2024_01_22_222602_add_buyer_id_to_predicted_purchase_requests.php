<?php

use App\Models\QuotesPortal\PredictedPurchaseRequest;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('predicted_purchase_requests', function (Blueprint $table) {
            $table->unsignedBigInteger('buyer_id')->nullable()->after('quote_number');
            $table->foreign('buyer_id')->references('id')->on('users');
        });

        PredictedPurchaseRequest::query()
            ->with([PredictedPurchaseRequest::RELATION_QUOTE])
            ->each(function (PredictedPurchaseRequest $predictedPurchaseRequest) {
                $predictedPurchaseRequest->buyer_id = $predictedPurchaseRequest->quote->buyer_id;
                $predictedPurchaseRequest->save();
            });

        Schema::table('predicted_purchase_requests', function (Blueprint $table) {
            $table->unsignedBigInteger('buyer_id')->nullable(false)->after('quote_number')->change();
        });
    }

    public function down(): void
    {
        Schema::table('predicted_purchase_requests', function (Blueprint $table) {
            $table->dropForeign(['buyer_id']);
            $table->dropColumn('buyer_id');
        });
    }
};
