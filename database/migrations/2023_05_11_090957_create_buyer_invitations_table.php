<?php

use App\Enums\InvitationStatusEnum;
use App\Models\BuyerInvitation;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(BuyerInvitation::TABLE_NAME, function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger(BuyerInvitation::BUYER_ID)
                ->comment('The ID of the user that received the invitation');
            $table->string(BuyerInvitation::TOKEN)->nullable();
            $table->dateTime(BuyerInvitation::REGISTERED_AT)->nullable();
            $table->string(BuyerInvitation::STATUS)->default(InvitationStatusEnum::PENDING);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(BuyerInvitation::TABLE_NAME);
    }
};
