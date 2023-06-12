<?php

use App\Models\CompanyUser;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(CompanyUser::TABLE_NAME, function (Blueprint $table) {
            $table->string(CompanyUser::BUYER_CODE, 10)->nullable();
        });

        User::query()->with(['companies'])->each(function (User $user) {
            foreach ($user->companies as $company) {
                $user->companies()->updateExistingPivot($company->id, [
                    CompanyUser::BUYER_CODE => $user->buyer_code,
                ]);
            }
        });
    }
};
