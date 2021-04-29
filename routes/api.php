<?php

use App\Http\Controllers\RestApiController;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('device_activation', [RestApiController::class, 'deviceActivation']);
Route::post('update_operator', [RestApiController::class, 'updateOperatorDetails']);
Route::post('athlete_data', [RestApiController::class, 'getParticipants']);
Route::post('attendance_sync', [RestApiController::class, 'syncAttendanceData']);
