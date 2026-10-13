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

Route::get('pesapalResonsez', 'PesapalController@index')->name('pesapalResonse');
//madekenya global routes
Route::post('loginz','Api_controllers\Auth_ApiController@login');

Route::get('pacelz/{date}/index','Api_controllers\ManagementController@pacel');
//mizigo according to date pacel_Date
Route::get('pacel_todayz/index','Api_controllers\ManagementController@pacel_today');

Route::get('pacel_Date2z/{date}/index','Api_controllers\ManagementController@pacel_Date2');


Route::get('delete_mizigoz/{date}/index','Api_controllers\ManagementController@delete_mizigo');

Route::get('delete_driver_routez/{date}/index','Api_controllers\ManagementController@delete_driver_route');

Route::get('delete_car_routez/{date}/index','Api_controllers\ManagementController@delete_car_route');


Route::get('delete_mizigo_historyz/{date}/index','Api_controllers\ManagementController@delete_mizigo_history');


Route::get('car_today_testz/{id}/index','Api_controllers\ManagementController@car_today_test'); 

Route::get('packing_date_newz/{id}/index','Api_controllers\ManagementController@packing_pacel_date_new');   


//---sajiri mzgo-------

Route::post('store_pacelz/save','Api_controllers\ManagementController@store_pacel');


// ---end--------------

// Route::get('pacel_today/{date}/index','Api_controllers\ManagementController@pacel_today');

//mizigo according to delivery
Route::get('delivery_searchz/{delivery}/index','Api_controllers\ManagementController@delivery_search');


Route::get('driverz/{date}/index','Api_controllers\ManagementController@driver');
Route::post('driverz/save','Api_controllers\ManagementController@driver_store');
Route::get('driver_assignz/{driver_id}/{car_id}/index','Api_controllers\ManagementController@driver_assign');

Route::get('carz/index','Api_controllers\ManagementController@car');
Route::post('carz/save','Api_controllers\ManagementController@car_store');
Route::post('car_selectionz/save','Api_controllers\ManagementController@car_selection');
Route::get('car_todayz/{id}/{date}/index','Api_controllers\ManagementController@car_today');
Route::get('car_closez/{id}/index','Api_controllers\ManagementController@car_close');
Route::get('arrived_car_datez/{date}/index','Api_controllers\ManagementController@arrived_car_date');



Route::post('packingz/save','Api_controllers\ManagementController@packing_store');
Route::post('unpackingz/save','Api_controllers\ManagementController@unpacking_store');

Route::get('packing_datez/{id}/index','Api_controllers\ManagementController@packing_pacel_date');
Route::get('packing_pacelz/{id}/{date}/{key}/index','Api_controllers\ManagementController@packing_pacel');
Route::get('packing_pacelz/{id}/{startDate}/{endDate}/{key}/index','Api_controllers\ManagementController@packing_pacel_new');
Route::get('search_pacelz/{id}/{startDate}/{endDate}/{key}/{word}/index','Api_controllers\ManagementController@packing_pacel_new_22');
Route::get('pacel_historyz/{id}/index','Api_controllers\ManagementController@pacel_history');
Route::get('total_car_moneyz/{id}/{date}/index','Api_controllers\ManagementController@money_recieved_car');

Route::get('total_car_moneyz/{id}/{startDate}/{endDate}/index','Api_controllers\ManagementController@money_recieved_car_new');


Route::get('manifest_pacel_lengthz/{id}/{startDate}/{endDate}/index','Api_controllers\ManagementController@pacel_packet_lenght_request_fx');


Route::get('manifest_pacel_lengthz/{id}/{startDate}/{endDate}/{packet_length}/index','Api_controllers\ManagementController@packing_pacel_new_by_length');



Route::get('manifest_pacelsz/{id}/{startDate}/{endDate}/index','Api_controllers\ManagementController@manifest_pacels_limit');


Route::get('manifest_pacels_lastz/{id}/{startDate}/{endDate}/{lastId}/index','Api_controllers\ManagementController@manifest_pacels_last_limit');




// Route::post('institution/save','Api_controllers\TechTransfer\PrimaryController@institution_store');   
// Route::post('institution/update/{id}','Api_controllers\TechTransfer\PrimaryController@institution_update');      


Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


