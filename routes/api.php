<?php

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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::post('/register', 'Controller@register');
Route::post('/login', 'Controller@login');
Route::get('/jobs', 'Controller@get_jobs');
Route::post('/{token}/add_job', 'Controller@add_job');
Route::post('/{token}/apply', 'Controller@apply_job');
Route::get('/{token}/applications/{provider_id}', 'Controller@get_applications');
Route::post('/{token}/update_status/{app_id}', 'Controller@update_status');
