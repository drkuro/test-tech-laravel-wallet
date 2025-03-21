<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecurringTransfert extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'start_at',
        'end_at',
        'frequency',
        'recipient_email',
        'amount',
        'reason',
        'user_id',
    ];

    /**
     * @return BelongsTo<User>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
