<?php
namespace App\Http\Controllers;

use App\Models\Ad;
use App\Http\Resources\AdResource;
use Illuminate\Http\Request;

class AdController extends Controller
{
    public function index(Request $request)
    {

        $adsQuery = Ad::query();

        $adsQuery->when(
            $request->has('operator') && Ad::isValidOperator($request->input('operator')),
            function ($query) use ($request) {
                return $query->where('operator', $request->input('operator'));
            }
        );

        $ads = $adsQuery->paginate(10);

        return AdResource::collection($ads);
    }

}
