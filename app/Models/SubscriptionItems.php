<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionItems extends Model
{
    use HasFactory;

    protected $table = 'subscription_items';

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }
    
}
