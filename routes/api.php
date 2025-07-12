<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdController;
use App\Http\Controllers\SimAdController;
use App\Http\Controllers\OfferController;

Route::get('/ads', [AdController::class, 'index']);

Route::post('/sim-ads', [SimAdController::class, 'store']);
Route::post('/sim-ads/{id}/offers', [OfferController::class, 'store']);
Route::get('/sim-ads/{id}/offers', [OfferController::class, 'index']);
