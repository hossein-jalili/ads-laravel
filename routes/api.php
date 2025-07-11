<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdController;

Route::get('/ads', [AdController::class, 'index']);
