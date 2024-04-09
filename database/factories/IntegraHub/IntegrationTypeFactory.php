<?php

namespace Database\Factories\IntegraHub;

use App\Enums\IntegraHub\IntegrationHandlingTypeEnum;
use App\Enums\IntegraHub\IntegrationTypeEnum;
use App\Models\IntegraHub\IntegrationType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class IntegrationTypeFactory extends Factory
{
    protected $model = IntegrationType::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            IntegrationType::COMPANY_ID => null,
            IntegrationType::CODE => fake()->unique()->slug(2),
            IntegrationType::DESCRIPTION => fake()->sentence(3),
            IntegrationType::TYPE => fake()->randomElement(IntegrationTypeEnum::values()),
            IntegrationType::HANDLING_TYPE => fake()->randomElement(IntegrationHandlingTypeEnum::values()),
            IntegrationType::TARGET_URL => fake()->url(),
            IntegrationType::IS_VISIBLE => true,
            IntegrationType::IS_ENABLED => true,
            IntegrationType::IS_SYNCHRONOUS => true,
            IntegrationType::ALLOWS_DUPLICATES => true,
            IntegrationType::HEADERS => [],
            IntegrationType::PATH_PARAMETERS => [],
            IntegrationType::AUTHORIZATION => [],
            IntegrationType::INTERVAL => null,
            IntegrationType::IS_RUNNING => false,
            IntegrationType::LAST_RUN_AT => null,
        ];
    }
}
