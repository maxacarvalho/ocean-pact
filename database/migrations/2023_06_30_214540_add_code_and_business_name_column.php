<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('code_and_business_name')
                ->virtualAs("CONCAT(TRIM(`code`), ' - ', TRIM(`business_name`))")
                ->after('code_code_branch_and_branch');
        });
    }
};
