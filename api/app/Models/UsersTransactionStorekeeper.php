<?php

namespace App\Models;

use App\Models\User;
use App\Models\StoreKeeper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UsersTransactionStorekeeper extends Model
{
    use HasFactory;

    protected $table = 'users_transactions_storekeepers';

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
        'sender_balance' => UsersTransactionStorekeeper::DECIMAL_CAST,
        'recipient_balance' => UsersTransactionStorekeeper::DECIMAL_CAST,
        'value_transaction' => UsersTransactionStorekeeper::DECIMAL_CAST,
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id', 'id');
    }

    public function recipient()
    {
        return $this->belongsTo(StoreKeeper::class, 'recipient_id', 'id');
    }
}
