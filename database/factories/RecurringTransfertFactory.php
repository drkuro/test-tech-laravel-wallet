<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\WalletTransfer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WalletTransfer>
 */
class RecurringTransfertFactory extends Factory
{
    public function definition(): array
    {
        return [
            'start_at' => fake()->date(),
            'end_at' => fake()->date(),
            'frequency' => fake()->numberBetween(0, 100),
            'recipient_email' => fake()->email(),
            'amount' => fake()->numberBetween(1, 100),
            'reason' => fake()->text(),
        ];
    }

    public function amount(int $amount): self
    {
        return $this->state(fn (array $attributes) => [
            'amount' => $amount,
        ]);
    }
}
