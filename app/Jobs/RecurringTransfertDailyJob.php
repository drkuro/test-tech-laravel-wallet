<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Actions\PerformWalletTransfer;
use App\Models\RecurringTransfert;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class RecurringTransfertDailyJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct() {}

    /**
     * Execute the job.
     */
    public function handle(PerformWalletTransfer $performWalletTransfer): void
    {
        $now = Carbon::now();
        $recurrings = RecurringTransfert::where('start_at', '<=', $now->toDateString())->where('end_at', '>=', $now->toDateString())->get();
        foreach ($recurrings as $recurring) {
            $start = Carbon::parse($recurring->start_at);
            $diff = $start->diffInDays($now) % $recurring->frequency;
            if ($diff === 0) {
                $recipient = User::where('email', '=', $recurring->recipient_email)->first();

                try {
                    if ($recipient) {
                        $performWalletTransfer->execute(
                            sender: $recurring->user,
                            recipient: $recipient,
                            amount: $recurring->amount,
                            reason: $recurring->reason,
                        );
                        Log::info('The automatic transfert is done');
                    }
                } catch (Exception $e) {
                    Log::info($e->getMessage());
                }
            } else {
                Log::info('diff not respected for '.$recurring->id.' skip');
            }
        }
    }
}
