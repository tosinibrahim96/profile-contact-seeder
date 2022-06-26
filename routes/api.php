<?php

use App\Http\Controllers\Trengo\ContactController;
use App\Http\Controllers\Trengo\ContactProfileController;
use App\Http\Controllers\Trengo\CustomFieldController;
use App\Http\Controllers\Trengo\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


/**
 * Trengo routes
 */
Route::prefix('trengo')->group(function () {
    Route::post('/custom_fields', [CustomFieldController::class, 'create']);
    Route::get('/custom_fields', [CustomFieldController::class, 'index']);


    Route::post('/create_profiles_from_file', [ProfileController::class, 'createProfilesFromFile']);
    Route::get('/profiles', [ProfileController::class, 'index']);

    Route::post('/create_contacts_from_file', [ContactController::class, 'createContactsFromFile']);
    Route::get('/contacts', [ContactController::class, 'index']);

    Route::post('/link_file_contacts_to_profiles', [ContactProfileController::class, 'linkContactsToProfiles']);
});
