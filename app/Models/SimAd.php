<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SimAd extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_name',
        'number',
        'price_suggestion',
        'city',
        'type',
    ];

    public function offers()
    {
        return $this->hasMany(Offer::class);
    }
}
