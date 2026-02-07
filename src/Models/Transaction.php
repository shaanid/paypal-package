<?php

namespace Shaanid\PayPal\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'transaction_id',
        'reference_id',
        'status',
        'amount',
        'currency',
        'payment_method',
        'payload',
        'user_id',
    ];

    /**
     * @var array
     */
    protected $casts = [
        'payload' => 'array',
    ];

    /**
     * Get the user that owns the transaction.
     */
    public function user()
    {
        $userModel = config('auth.providers.users.model', 'App\Models\User');
        return $this->belongsTo($userModel);
    }
}
