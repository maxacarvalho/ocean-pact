<?php

use App\Models\QuotesPortal\SupplierUser;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $users = User::query()
            ->whereNotNull('supplier_id')
            ->get();

        /** @var User $user */
        foreach ($users as $user) {
            $relation = SupplierUser::query()
                ->where(SupplierUser::USER_ID, '=', $user->id)
                ->where(SupplierUser::SUPPLIER_ID, '=', $user->supplier_id)
                ->exists();

            if ($relation) {
                continue;
            }

            SupplierUser::query()
                ->create([
                    SupplierUser::USER_ID => $user->id,
                    SupplierUser::SUPPLIER_ID => $user->supplier_id,
                    SupplierUser::CODE => 'XXXX',
                ]);
        }
    }
};
