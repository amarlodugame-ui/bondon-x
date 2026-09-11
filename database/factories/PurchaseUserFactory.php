<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<User>
 */
class PurchaseUserFactory extends Factory
{
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'member_no' => fake()->unique()->numerify('QA######'),
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'mobile' => fake()->unique()->numerify('017########'),
            'referral_code' => fake()->unique()->bothify('QA########'),
            'password' => 'purchase-test-password',
            'status' => 0,
            'is_deleted' => false,
            'balance' => '10000.00',
            'saving_balance' => '5000.00',
        ];
    }
}
