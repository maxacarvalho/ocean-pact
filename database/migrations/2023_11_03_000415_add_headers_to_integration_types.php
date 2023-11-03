<?php

use App\Models\IntegraHub\IntegrationType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('integration_types', function (Blueprint $table) {
            $table->json('headers')->after('allows_duplicates');
        });

        IntegrationType::query()->update([
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    public function down(): void
    {
        Schema::table('integration_types', function (Blueprint $table) {
            $table->dropColumn('headers');
        });
    }
};
