<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Subscription extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'end_date',
        'is_active'
    ];

    protected $casts = [
        'end_date' => 'datetime',
        'is_active' => 'boolean',
    ];
    
    public static function boot() {
        parent::boot();

        static::creating(function ($model) {
            $model->user_id = Auth::user()->id;
            $model->end_date = now()->addDays(30);
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subscriptionPayment()
    {
        return $this->hasOne(SubscriptionPayment::class);
    }
}
