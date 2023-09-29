<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('buyer_invitations', function (Blueprint $table) {
            $table->dateTime('sent_at')->nullable()->after('registered_at');
        });
    }
};
