<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\PerformWalletTransfer;
use App\Http\Requests\RecurringTransfertRequest;
use App\Models\RecurringTransfert;
use Illuminate\Http\Request;

class RecurringTransfertController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $reccurings = $request->user()->recurrings;

        return view('recurring.index', compact('reccurings'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RecurringTransfertRequest $request, PerformWalletTransfer $performWalletTransfer)
    {

        $recipient = $request->getRecipient();

        $transfer = RecurringTransfert::create([
            'start_at' => $request->input('start_at'),
            'end_at' => $request->input('end_at'),
            'frequency' => $request->input('frequency'),
            'recipient_email' => $recipient->email,
            'amount' => $request->getAmountInCents(),
            'reason' => $request->input('reason'),
            'user_id' => $request->user()->id,
        ]);

        try {
            $performWalletTransfer->execute(
                sender: $request->user(),
                recipient: $recipient,
                amount: $request->getAmountInCents(),
                reason: $request->input('reason'),
            );
        } catch (InsufficientBalance $exception) {
            return redirect()->back()->with('money-sent-status', 'insufficient-balance')
                ->with('money-sent-recipient-name', $recipient->name)
                ->with('money-sent-amount', $request->getAmountInCents());
        }

        return redirect(route('recurring', absolute: false));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $reccuring = RecurringTransfert::find($id);
        if ($reccuring) {
            $reccuring->delete();
        }

        return redirect(route('recurring', absolute: false));
    }
}
