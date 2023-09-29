<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('integration_types', function (Blueprint $table) {
            $table->string('handling_type')->nullable()->change();
        });
    }
};
