<?php

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompanyFactory extends Factory
{
    protected $model = Company::class;

    public function definition(): array
    {
        return [
            Company::CODE => $this->faker->unique()->regexify('[0-9]{4}'),
            Company::BRANCH => $this->faker->unique()->regexify('[0-9]{2}'),
            Company::CNPJ => $this->faker->cnpj(),
            Company::DESCRIPTION => $this->faker->company(),
            Company::LEGAL_NAME => $this->faker->company(),
            Company::TRADE_NAME => $this->faker->company(),
        ];
    }
}
