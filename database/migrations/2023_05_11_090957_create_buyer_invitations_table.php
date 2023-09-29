<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('buyer_invitations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('buyer_id')
                ->comment('The ID of the user that received the invitation');
            $table->string('token')->nullable();
            $table->dateTime('registered_at')->nullable();
            $table->string('status')->default('PENDING');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('buyer_invitations');
    }
};
