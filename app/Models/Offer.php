<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    use HasFactory;

    protected $fillable = [
        'sim_ad_id',
        'bidder_name',
        'price',
    ];

    public function simAd()
    {
        return $this->belongsTo(SimAd::class);
    }
}
