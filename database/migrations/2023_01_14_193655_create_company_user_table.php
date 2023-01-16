<?php

use App\Models\CompanyUser;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(CompanyUser::TABLE_NAME, static function (Blueprint $table) {
            $table->unsignedBigInteger(CompanyUser::COMPANY_ID);
            $table->unsignedBigInteger(CompanyUser::USER_ID);

            $table->primary([CompanyUser::COMPANY_ID, CompanyUser::USER_ID]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(CompanyUser::TABLE_NAME);
    }
};
