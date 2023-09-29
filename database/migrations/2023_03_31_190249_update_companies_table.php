<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', static function (Blueprint $table) {
            $table->renameColumn('cnpj', 'cnpj_cpf');
            $table->renameColumn('description', 'branch');
            $table->renameColumn('legal_name', 'business_name');
            $table->renameColumn('trade_name', 'name');
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->string('code', 10)->index()->change();

            $table->string('code_branch', 10)->index()->change();

            $table->string('cnpj_cpf', 14)->change();

            $table->string('branch', 100)->change();

            $table->string('business_name', 100)->change();

            $table->string('name', 100)->change();

            $table->string('phone_number', 100)->nullable();
            $table->string('fax_number', 100)->nullable();
            $table->string('state_inscription', 100)->nullable();
            $table->string('inscm', 100)->nullable();
            $table->string('address', 100)->nullable();
            $table->string('complement', 100)->nullable();
            $table->string('neighborhood', 100)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('state', 2)->nullable();
            $table->string('postal_code', 8)->nullable();
            $table->string('city_code', 100)->nullable();
            $table->string('cnae', 100)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            //
        });
    }
};
