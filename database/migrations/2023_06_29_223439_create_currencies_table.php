<?php

use App\Models\Currency;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(Currency::TABLE_NAME, function (Blueprint $table) {
            $table->id();
            $table->string(Currency::COMPANY_CODE);
            $table->integer(Currency::PROTHEUS_CURRENCY_ID);
            $table->string(Currency::DESCRIPTION);
            $table->string(Currency::PROTHEUS_CODE);
            $table->string(Currency::PROTHEUS_ACRONYM);
            $table->string(Currency::ISO_CODE);
            $table->timestamps();
        });
    }
};
