<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsersTransaction extends Model
{
    use HasFactory;

    const DECIMAL_CAST = 'decimal: 2';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'sender_id',
        'recipient_id',
        'value_transaction',
        'sender_balance',
        'recipient_balance',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'sender_balance' => UsersTransaction::DECIMAL_CAST,
        'recipient_balance' => UsersTransaction::DECIMAL_CAST,
        'value_transaction' => UsersTransaction::DECIMAL_CAST,
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id', 'id');
    }

    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_id', 'id');
    }
}
