<?php

use App\Models\BuyerInvitation;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(BuyerInvitation::TABLE_NAME, function (Blueprint $table) {
            $table->dateTime(BuyerInvitation::SENT_AT)->nullable()->after(BuyerInvitation::REGISTERED_AT);
        });
    }
};
