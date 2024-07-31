<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Cashier\Subscription as CashierSubscription;

class Subscriptions extends CashierSubscription
{
    use HasFactory;

    protected $table = 'subscriptions';

    public function items()
    {
        return $this->hasMany(SubscriptionItem::class);
    }
}
