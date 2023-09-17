<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payload_processing_attempts', static function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('payload_id')->index();
            $table->string('status')->index();
            $table->text('message')->nullable();
            $table->timestamps();

            $table->index('created_at');
            $table->index('updated_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payload_processing_attempts');
    }
};
