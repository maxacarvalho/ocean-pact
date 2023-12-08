<?php

use App\Models\IntegraHub\IntegrationType;
use App\Models\IntegraHub\IntegrationTypeField;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('integration_type_fields', function (Blueprint $table) {
            $table->integer('order_column')
                ->default(1)
                ->after('integration_type_id')
                ->index();
        });

        /** @var IntegrationType[]|Collection|array $integrationTypes */
        $integrationTypes = IntegrationType::query()->get();

        foreach ($integrationTypes as $integrationType) {
            /** @var IntegrationTypeField[]|Collection|array $fields */
            $fields = $integrationType->fields()->orderBy('id')->get();

            $orderColumn = 1;
            foreach ($fields as $field) {
                $field->order_column = $orderColumn++;
                $field->save();
            }
        }
    }

    public function down(): void
    {
        Schema::table('integration_type_fields', function (Blueprint $table) {
            $table->dropColumn('order_column');
        });
    }
};
