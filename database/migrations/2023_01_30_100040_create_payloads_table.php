<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payloads', static function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('integration_type_id')->index();
            $table->json('payload');
            $table->timestamp('stored_at')->nullable()->index();
            $table->string('stored_status')->default('STORED');
            $table->timestamp('processed_at')->nullable()->index();
            $table->string('processed_status')->nullable();
            $table->timestamps();

            $table->index('created_at');
            $table->index('updated_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payloads');
    }
};
