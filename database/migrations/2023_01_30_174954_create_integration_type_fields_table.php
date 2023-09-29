<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('integration_type_fields', static function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('integration_type_id')->index();
            $table->string('field_name');
            $table->string('field_type');
            $table->json('field_rules')->nullable();
            $table->timestamps();

            $table->index('created_at');
            $table->index('updated_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('integration_type_fields');
    }
};
