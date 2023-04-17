<?php

use App\Models\IntegrationType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(IntegrationType::TABLE_NAME, function (Blueprint $table) {
            $table->string(IntegrationType::HANDLING_TYPE)->nullable()->change();
        });
    }
};
