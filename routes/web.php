<?php

use App\Http\Controllers\LeadController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => to_route('leads.create'));
Route::resource('leads', LeadController::class)->only(['create', 'store']);
