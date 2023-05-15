<?php

use App\Enums\InvitationStatusEnum;
use App\Models\Quote;
use App\Models\Supplier;
use App\Models\SupplierInvitation;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(SupplierInvitation::TABLE_NAME, function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger(SupplierInvitation::SUPPLIER_ID);
            $table->unsignedBigInteger(SupplierInvitation::QUOTE_ID);
            $table->string(SupplierInvitation::TOKEN);
            $table->timestamp(SupplierInvitation::SENT_AT)->nullable();
            $table->string(SupplierInvitation::STATUS)->default(InvitationStatusEnum::PENDING());
            $table->timestamps();

            $table->foreign(SupplierInvitation::SUPPLIER_ID)
                ->references(Supplier::ID)
                ->on(Supplier::TABLE_NAME)
                ->cascadeOnDelete();

            $table->foreign(SupplierInvitation::QUOTE_ID)
                ->references(Quote::ID)
                ->on(Quote::TABLE_NAME)
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(SupplierInvitation::TABLE_NAME);
    }
};
