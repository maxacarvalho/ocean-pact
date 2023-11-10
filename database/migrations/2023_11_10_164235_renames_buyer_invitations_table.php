<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('buyer_invitations', 'user_invitations');
    }

    public function down(): void
    {
        Schema::rename('user_invitations', 'buyer_invitations');
    }
};
