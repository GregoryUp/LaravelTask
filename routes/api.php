<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ClientController;
use App\Http\Controllers\CarController;

Route::resource('clients', ClientController::class);
Route::resource('cars', CarController::class);
Route::get('parking', [CarController::class, 'getParkingStatus']);