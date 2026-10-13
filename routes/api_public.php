<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public API routes (no auth:sanctum / auth:api)
|--------------------------------------------------------------------------
|
| Loaded BEFORE routes/api.php so these URLs are not blocked when the main
| API file is wrapped in authentication middleware on the server.
| Mobile app uses user_id in the body for company scoping (same pattern
| as other ManagementController APIs).
|
*/

Route::post('driver/save', 'Api_controllers\ManagementController@driver_store');
