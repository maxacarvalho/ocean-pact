<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', static function (Blueprint $table) {
            $table->id();
            $table->string('code', 10);
            $table->string('code_branch', 10);
            $table->string('cnpj', 18)->unique();
            $table->string('description', 255);
            $table->string('legal_name', 255);
            $table->string('trade_name', 255);
            $table->timestamps();

            $table->unique(['code', 'code_branch']);
            $table->index('created_at');
            $table->index('updated_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
