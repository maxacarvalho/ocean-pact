<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('company_code');
            $table->integer('protheus_currency_id');
            $table->string('description');
            $table->string('protheus_code');
            $table->string('protheus_acronym');
            $table->string('iso_code');
            $table->timestamps();
        });
    }
};
