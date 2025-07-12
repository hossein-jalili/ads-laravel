<?php

namespace App\Http\Controllers;

use App\Http\Resources\OfferResource;
use App\Http\Resources\SimAdResource;
use App\Models\SimAd;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SimAdController extends Controller
{
    public function store(Request $request)
    {

        $validated = $request->validate([
            'owner_name' => ['required', 'string', 'max:50', 'regex:/^[\x{0600}-\x{06FF} ]+$/u'],
            'number' => ['required', 'string', 'size:11', 'regex:/^09\d{9}$/', Rule::unique('sim_ads')],
            'price_suggestion' => ['required', 'numeric', 'min:10000'],
            'city' => ['required', 'string', 'min:2', 'regex:/^[\x{0600}-\x{06FF} ]+$/u'],
            'type' => ['required', 'in:custom_offer,instant_sale'],
        ]);

        $simAd = SimAd::create([
            'owner_name' => $validated['owner_name'],
            'number' => $validated['number'],
            'price_suggestion' => $validated['price_suggestion'],
            'city' => $validated['city'],
            'type' => $validated['type'],
        ]);

        return new SimAdResource($simAd);
    }
}
