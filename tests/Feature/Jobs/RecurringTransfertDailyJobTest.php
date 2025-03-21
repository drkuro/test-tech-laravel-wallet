<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Actions\PerformWalletTransfer;
use App\Jobs\RecurringTransfertDailyJob;
use App\Models\RecurringTransfert;
use App\Models\User;
use App\Models\Wallet;
use Carbon\Carbon;

use function Pest\Laravel\assertDatabaseCount;

beforeEach(function () {
    $this->action = app(PerformWalletTransfer::class);
});
test('launch a successful transfert', function () {

    $sender = User::factory()->create();
    $recipient = User::factory()->create();

    Wallet::factory()->for($sender)->richChillGuy()->create();
    Wallet::factory()->for($recipient)->create();

    RecurringTransfert::factory()->create([
        'start_at' => Carbon::now()->subDays(5),
        'end_at' => Carbon::now()->addDays(5),
        'frequency' => 5,
        'recipient_email' => $recipient->email,
        'amount' => 1000,
        'reason' => 'Test Transfer',
        'user_id' => $sender->id,
    ]);

    $job = new RecurringTransfertDailyJob;
    $job->handle($this->action);
    $this->assertTrue(true);

    assertDatabaseCount('wallet_transfers', 1);
    expect($recipient->refresh()->wallet->balance)->toBe(1000);
});

test('launch a transfert with insuffisant balaance', function () {
    $sender = User::factory()->create();
    $recipient = User::factory()->create();

    Wallet::factory()->for($sender)->create();
    Wallet::factory()->for($recipient)->create();

    RecurringTransfert::factory()->create([
        'start_at' => Carbon::now()->subDays(5),
        'end_at' => Carbon::now()->addDays(5),
        'frequency' => 5,
        'recipient_email' => $recipient->email,
        'amount' => 1000,
        'reason' => 'Test Transfer',
        'user_id' => $sender->id,
    ]);

    $job = new RecurringTransfertDailyJob;
    $job->handle($this->action);

    $this->assertTrue(true);
    assertDatabaseCount('wallet_transfers', 0);
    expect($recipient->refresh()->wallet->balance)->toBe(0);
});

test('launch a transfert with balance but not the good dau', function () {
    $sender = User::factory()->create();
    $recipient = User::factory()->create();

    Wallet::factory()->for($sender)->richChillGuy()->create();
    Wallet::factory()->for($recipient)->create();

    RecurringTransfert::factory()->create([
        'start_at' => Carbon::now()->subDays(5),
        'end_at' => Carbon::now()->addDays(5),
        'frequency' => 15,
        'recipient_email' => $recipient->email,
        'amount' => 1000,
        'reason' => 'Test Transfer',
        'user_id' => $sender->id,
    ]);

    $job = new RecurringTransfertDailyJob;
    $job->handle($this->action);

    $this->assertTrue(true);
    assertDatabaseCount('wallet_transfers', 0);
    expect($recipient->refresh()->wallet->balance)->toBe(0);
});
