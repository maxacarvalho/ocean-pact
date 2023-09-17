<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('integration_types', static function (Blueprint $table) {
            $table->id();
            $table->string('description');
            $table->string('type', 20);
            $table->string('handling_type', 50);
            $table->string('target_url');
            $table->timestamps();

            $table->index('created_at');
            $table->index('updated_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('integration_types');
    }
};
