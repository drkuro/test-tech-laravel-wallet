<?php

declare(strict_types=1);

use App\Models\User;
use App\Models\Wallet;
use Carbon\Carbon;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\post;

test('create a new recurring transfert', function () {
    $user = User::factory()->has(Wallet::factory()->richChillGuy())->create();
    $other = User::factory([
        'email' => 'another.guy@test.fr',
    ])->has(Wallet::factory()->richChillGuy())->create();
    $wallet = Wallet::factory()->richChillGuy()->for($user)->create();

    actingAs($user);
    Log::info(Carbon::now()->toDateString());
    $response = post('/recurring', [
        'start_at' => Carbon::now()->toDateString(),
        'end_at' => Carbon::now()->addMonth()->toDateString(),
        'frequency' => 10,
        'recipient_email' => 'another.guy@test.fr',
        'amount' => 10.00,
        'reason' => 'unitTest',
    ]);

    assertDatabaseCount('recurring_transferts', 1);
    $response->assertRedirect(route('recurring', absolute: false));
});
