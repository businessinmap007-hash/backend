<?php

use Illuminate\Support\Facades\Route;

// Public General Routes
Route::get('countries', 'App\Http\Controllers\Api\V1\LocationController@countries');
Route::get('cities/{location}', 'App\Http\Controllers\Api\V1\LocationController@cities');

// Currency Converter
Route::get('currency/converter', function (\Illuminate\Http\Request $request) {
    return response()->json([
        'status' => 200,
        'total' => currencyConverter($request->from, $request->to, $request->amount)
    ]);
});
