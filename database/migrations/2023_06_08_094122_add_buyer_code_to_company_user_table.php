<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('company_user', function (Blueprint $table) {
            $table->string('buyer_code', 10)->nullable();
        });

        User::query()->with(['companies'])->each(function (User $user) {
            foreach ($user->companies as $company) {
                $user->companies()->updateExistingPivot($company->id, [
                    'buyer_code' => $user->buyer_code,
                ]);
            }
        });
    }
};
