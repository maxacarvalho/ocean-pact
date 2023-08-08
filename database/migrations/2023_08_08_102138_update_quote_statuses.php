<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('quotes')
            ->where('status', 'DRAFT')
            ->update(['status' => 'PENDING']);

        DB::table('quotes')
            ->where('status', 'ACCEPTED')
            ->update(['status' => 'ANALYZED']);

        DB::table('quotes')
            ->where('status', 'REJECTED')
            ->update(['status' => 'ANALYZED']);
    }
};
