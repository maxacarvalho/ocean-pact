<?php

namespace Database\Factories\IntegraHub;

use App\Enums\IntegraHub\IntegrationTypeFieldTypeEnum;
use App\Models\IntegraHub\IntegrationTypeField;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class IntegrationTypeFieldFactory extends Factory
{
    protected $model = IntegrationTypeField::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            IntegrationTypeField::INTEGRATION_TYPE_ID => IntegrationTypeFactory::new(),
            IntegrationTypeField::ORDER_COLUMN => fake()->numberBetween(1, 10),
            IntegrationTypeField::FIELD_NAME => fake()->word(),
            IntegrationTypeField::FIELD_TYPE => fake()->randomElement(IntegrationTypeFieldTypeEnum::values()),
            IntegrationTypeField::FIELD_RULES => null,
            IntegrationTypeField::TARGET_INTEGRATION_TYPE_FIELD_ID => null,
            IntegrationTypeField::CREATED_AT => fake()->dateTime(),
            IntegrationTypeField::UPDATED_AT => fake()->dateTime(),
        ];
    }
}
