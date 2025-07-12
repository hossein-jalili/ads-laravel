<?php

namespace App\Http\Controllers;

use App\Http\Resources\AdResource;
use App\Http\Resources\OfferResource;
use Illuminate\Http\Request;
use App\Models\Offer;
use App\Models\SimAd;

class OfferController extends Controller
{
    public function store(Request $request, $id)
    {
        $validated = $request->validate([
            'price' => ['required', 'numeric', 'gt:' . SimAd::find($id)->price_suggestion],
            'bidder_name' => ['required', 'string', 'max:50', 'regex:/^[\x{0600}-\x{06FF} ]+$/u'],
        ]);


        $simAd = SimAd::findOrFail($id);


        $offer = Offer::create([
            'sim_ad_id' => $simAd->id,
            'bidder_name' => $validated['bidder_name'],
            'price' => $validated['price'],
        ]);
        return new OfferResource($offer);
    }

    public function index($id)
    {

        $offers = Offer::where('sim_ad_id', $id)
            ->orderBy('price', request('sort', 'asc'))
            ->get();

        return OfferResource::collection($offers);
    }
}
