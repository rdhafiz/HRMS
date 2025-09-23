<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\MicrosoftAuthController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Microsoft OAuth routes (web routes for redirects)
Route::get('/auth/microsoft', [MicrosoftAuthController::class, 'redirectToMicrosoft']);
Route::get('/auth/microsoft/callback', [MicrosoftAuthController::class, 'handleMicrosoftCallback']);

// Route::get('/', function () {
//     return view('welcome');
// });

// SPA catch-all to serve the Vue app
Route::view('/{any}', 'app')->where('any', '.*');
