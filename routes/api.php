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

Route::get('pesapalResonse', 'PesapalController@index')->name('pesapalResonse');
//madekenya global routes
Route::post('login','Api_controllers\Auth_ApiController@login');
Route::post('logout','Api_controllers\Auth_ApiController@logout');

// Mobile App Settings
Route::get('mobile_settings',[App\Http\Controllers\MobileSettingsController::class, 'getSettings']);
Route::post('mobile_settings/update',[App\Http\Controllers\MobileSettingsController::class, 'updateSettings']);

/*
 * Mikoa (regions) by company_id
 * Sample: GET /api/mikoa/index?company_id=1
 * Or:     GET /api/mikoa/index?user_id=5  (company_id derived from user)
 * Response: { "success": true, "error": false, "message": "successfully", "mikoa": [ { "id": 1, "company_id": 1, "regional_name": "Dar es Salaam" }, ... ] }
 */
Route::get('mikoa', 'Api_controllers\ManagementController@mikoa');

Route::get('pacel/{date}/index','Api_controllers\ManagementController@pacel');
//mizigo according to date pacel_Date
Route::get('pacel_today/index','Api_controllers\ManagementController@pacel_today');
Route::get('total_pacel/index','Api_controllers\ManagementController@total_pacel');

Route::get('pacel_Date2/{date}/index','Api_controllers\ManagementController@pacel_Date2');


Route::get('delete_mizigo/{date}/index','Api_controllers\ManagementController@delete_mizigo');

Route::get('delete_driver_route/{date}/index','Api_controllers\ManagementController@delete_driver_route');

Route::get('delete_car_route/{date}/index','Api_controllers\ManagementController@delete_car_route');


Route::get('delete_mizigo_history/{date}/index','Api_controllers\ManagementController@delete_mizigo_history');


Route::get('car_today_test/{id}/index','Api_controllers\ManagementController@car_today_test'); 

Route::get('packing_date_new/{id}/index','Api_controllers\ManagementController@packing_pacel_date_new');   


//---sajiri mzgo-------

Route::post('store_pacel/save','Api_controllers\ManagementController@store_pacel');


// ---end--------------

// Route::get('pacel_today/{date}/index','Api_controllers\ManagementController@pacel_today');

//mizigo according to delivery
Route::get('delivery_search/{delivery}/index','Api_controllers\ManagementController@delivery_search');


Route::get('driver/{date}/index','Api_controllers\ManagementController@driver');
// POST driver/save is registered in routes/api_public.php (no auth middleware)
Route::get('driver_assign/{driver_id}/{car_id}/index','Api_controllers\ManagementController@driver_assign');

Route::get('car/index','Api_controllers\ManagementController@car');
Route::post('car/save','Api_controllers\ManagementController@car_store');
Route::post('car_selection/save','Api_controllers\ManagementController@car_selection');
Route::get('car_today/{id}/{date}/index','Api_controllers\ManagementController@car_today');
Route::get('car_close/{id}/index','Api_controllers\ManagementController@car_close');
Route::get('arrived_car_date/{date}/index','Api_controllers\ManagementController@arrived_car_date');
Route::post('car/arrive','Api_controllers\ManagementController@car_arrive');
Route::post('car/start_trip','Api_controllers\ManagementController@car_start_trip');

// Drop off point APIs
Route::get('drop_off_point/cars','Api_controllers\ManagementController@drop_off_point_cars');
Route::post('drop_off_point/save','Api_controllers\ManagementController@drop_off_point_store');
Route::get('drop_off_point/list','Api_controllers\ManagementController@drop_off_point_list');
Route::post('drop_off_point/update','Api_controllers\ManagementController@drop_off_point_update');
Route::get('drop_off_point/parcels','Api_controllers\ManagementController@drop_off_point_parcels');
Route::post('drop_off_point/drop_parcel','Api_controllers\ManagementController@drop_parcel');
Route::post('drop_off_point/update_parcel_payment','Api_controllers\ManagementController@update_parcel_payment');
Route::get('drop_off_point/parcels_history','Api_controllers\ManagementController@drop_off_parcels_history');
Route::post('drop_off_point/pay_remaining','Api_controllers\ManagementController@pay_remaining_balance');



Route::post('packing/save','Api_controllers\ManagementController@packing_store');
Route::get('customer/packing_sublist','Api_controllers\ManagementController@customer_packing_sublist');
Route::post('unpacking/save','Api_controllers\ManagementController@unpacking_store');

Route::get('packing_date/{id}/index','Api_controllers\ManagementController@packing_pacel_date');
Route::get('packing_pacel/{id}/{date}/{key}/index','Api_controllers\ManagementController@packing_pacel');
Route::get('packing_pacel/{id}/{startDate}/{endDate}/{key}/index','Api_controllers\ManagementController@packing_pacel_new');
Route::get('search_pacel/{id}/{startDate}/{endDate}/{key}/{word}/index','Api_controllers\ManagementController@packing_pacel_new_22');
Route::get('pacel_history/{id}/index','Api_controllers\ManagementController@pacel_history');
Route::get('total_car_money/{id}/{date}/index','Api_controllers\ManagementController@money_recieved_car');

Route::get('total_car_money/{id}/{startDate}/{endDate}/index','Api_controllers\ManagementController@money_recieved_car_new');


Route::get('manifest_pacel_length/{id}/{startDate}/{endDate}/index','Api_controllers\ManagementController@pacel_packet_lenght_request_fx');


Route::get('manifest_pacel_length/{id}/{startDate}/{endDate}/{packet_length}/index','Api_controllers\ManagementController@packing_pacel_new_by_length');



Route::get('manifest_pacels/{id}/{startDate}/{endDate}/index','Api_controllers\ManagementController@manifest_pacels_limit');


Route::get('manifest_pacels_last/{id}/{startDate}/{endDate}/{lastId}/index','Api_controllers\ManagementController@manifest_pacels_last_limit');




// Route::post('institution/save','Api_controllers\TechTransfer\PrimaryController@institution_store');   
// Route::post('institution/update/{id}','Api_controllers\TechTransfer\PrimaryController@institution_update');      


Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


