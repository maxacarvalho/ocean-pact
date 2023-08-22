<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->after('is_draft', function (Blueprint $table) {
                $table->string('locale')->default('pt_BR');
                $table->string('currency')->default('BRL');
            });
        });
    }
};
