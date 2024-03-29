<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stake extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'spender',
        'amount',
        'type'
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
