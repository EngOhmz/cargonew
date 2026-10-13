<?php

//use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Mail\WelcomeMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\MailerController;
use App\Http\Controllers\CompanyLogoController;
use App\Http\Controllers\MobileSettingsController;

// Authentication routes (login, register, password reset, etc.) - accessible without auth
Auth::routes();

// Company registration route - accessible without auth
Route::get('/register_ema', [App\Http\Controllers\Auth\RegisterEmasuiteController::class, 'showRegistrationForm'])->name('register_ema');
Route::post('/register_ema', [App\Http\Controllers\Auth\RegisterEmasuiteController::class, 'register'])->name('register_ema.post');

// Root route - shows login page, accessible without auth
Route::get('/', function () {
    return view('auth.login');
});

// JSON API-style mikoa endpoint for mobile (no auth session required)
Route::get('mikoa', [App\Http\Controllers\Api_controllers\ManagementController::class, 'mikoa']);

// All other routes require authentication
Route::middleware(['auth', 'tenant.active'])->group(function () {

    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

    // Company Logo routes
    Route::get('company-logo', [CompanyLogoController::class, 'index'])->name('company-logo.index');
    Route::post('company-logo', [CompanyLogoController::class, 'store'])->name('company-logo.store');
    Route::delete('company-logo', [CompanyLogoController::class, 'destroy'])->name('company-logo.destroy');

    // Mobile Settings
    Route::get('mobile_settings', [MobileSettingsController::class, 'index'])->name('mobile_settings.index');
    Route::put('mobile_settings', [MobileSettingsController::class, 'update'])->name('mobile_settings.update');

    // Document settings (branch, contact, tunasafirisha text for delivery/print)
    Route::get('document_settings', [App\Http\Controllers\DocumentSettingsController::class, 'index'])->name('document_settings.index');
    Route::put('document_settings', [App\Http\Controllers\DocumentSettingsController::class, 'update'])->name('document_settings.update');

    // Sajili mikoa (regions by company) - web page under /mikoa/manage
    Route::get('mikoa/manage', [App\Http\Controllers\MikoaController::class, 'index'])->name('mikoa.index');
    Route::post('mikoa/manage', [App\Http\Controllers\MikoaController::class, 'store'])->name('mikoa.store');
    Route::delete('mikoa/manage/{id}', [App\Http\Controllers\MikoaController::class, 'destroy'])->name('mikoa.destroy');

    // Cargo routes (from mikoa → to mikoa) for registration
    Route::get('cargo-routes/manage', [App\Http\Controllers\CargoRouteController::class, 'index'])->name('cargo_routes.index');
    Route::post('cargo-routes/manage', [App\Http\Controllers\CargoRouteController::class, 'store'])->name('cargo_routes.store');
    Route::post('cargo-routes/manage/{route}/stops', [App\Http\Controllers\CargoRouteController::class, 'storeStop'])->name('cargo_routes.stops.store');
    Route::delete('cargo-routes/manage/{route}/stops/{stop}', [App\Http\Controllers\CargoRouteController::class, 'destroyStop'])->name('cargo_routes.stops.destroy');
    Route::delete('cargo-routes/manage/{id}', [App\Http\Controllers\CargoRouteController::class, 'destroy'])->name('cargo_routes.destroy');

    // Registered Companies (Admin only)
    Route::resource('registered_companies', 'RegisteredCompanyController')->only(['index', 'edit', 'update']);
    Route::put('registered_companies/{id}/toggle_disabled', [App\Http\Controllers\RegisteredCompanyController::class, 'toggleDisabled'])->name('registered_companies.toggle_disabled');

    // SMS Billing (platform admin only)
    Route::get('sms-billing', [App\Http\Controllers\SmsBillingController::class, 'index'])->name('sms_billing.index');
    Route::get('sms-billing/{id}', [App\Http\Controllers\SmsBillingController::class, 'show'])->name('sms_billing.show');

    // Summary Dashboard
    Route::group(['prefix' => 'summary'], function(){
        Route::get('dashboard', 'Summary\\DashboardController@index')->name('summary.dashboard');
    });

    // Authorization routes
    Route::group(['prefix' => 'authorization'], function(){
        Route::resource('permissions', 'authorization\PermissionController');
        Route::resource('roles', 'authorization\RoleController');
        //Route::resource('users', 'authorization\UsersController');
        Route::resource('departments', 'DepartmentController');
        Route::resource('designations', 'DesignationController');
        Route::get('findDepartment', 'authorization\UsersController@findDepartment');  
    });

    // Customer routes
    Route::resource('list_ya_wateja','Customer\CustomerList');
    Route::resource('list_ya_mizigo','Customer\PacelList');
    
    // Revenue Report routes
    Route::group(['prefix' => 'reports'], function() {
        Route::get('revenue', [App\Http\Controllers\Reports\RevenueReportController::class, 'index'])->name('reports.revenue.index');
        Route::get('revenue/pdf', [App\Http\Controllers\Reports\RevenueReportController::class, 'exportPdf'])->name('reports.revenue.pdf');
        Route::get('revenue/excel', [App\Http\Controllers\Reports\RevenueReportController::class, 'exportExcel'])->name('reports.revenue.excel');

        Route::get('car-parcels', [App\Http\Controllers\Reports\CarParcelsReportController::class, 'index'])->name('reports.car_parcels.index');
        Route::get('car-parcels/pdf', [App\Http\Controllers\Reports\CarParcelsReportController::class, 'exportPdf'])->name('reports.car_parcels.pdf');
    });

    // Delivery Agent routes
    Route::group(['prefix' => 'management'], function() {
        Route::get('delivery_agents', [App\Http\Controllers\Management\DeliveryAgentController::class, 'index'])->name('delivery_agents.index');
        Route::get('delivery_agents/create', [App\Http\Controllers\Management\DeliveryAgentController::class, 'create'])->name('delivery_agents.create');
        Route::post('delivery_agents', [App\Http\Controllers\Management\DeliveryAgentController::class, 'store'])->name('delivery_agents.store');
        Route::get('delivery_agents/{id}/edit', [App\Http\Controllers\Management\DeliveryAgentController::class, 'edit'])->name('delivery_agents.edit');
        Route::put('delivery_agents/{id}', [App\Http\Controllers\Management\DeliveryAgentController::class, 'update'])->name('delivery_agents.update');
        Route::get('delivery_agents/{id}/approve', [App\Http\Controllers\Management\DeliveryAgentController::class, 'approve'])->name('delivery_agents.approve');
    });
    Route::get('list_ya_wateja/{id}/detail','Customer\CustomerList@detail')->name('list_ya_wateja.detail');
    Route::get('list_ya_wateja/{id}/editmzigo','DashboardController@editmzigo')->name('list_ya_wateja.editmzigo');
    Route::post('mizigo_update','DashboardController@editmzigolist')->name('mizigo.kuongeza');
    Route::get('customer_pacel/{id}/edit', 'DashboardController@editPacel')->name('customer.pacel.edit');
    Route::post('customer_pacel/update', 'DashboardController@updatePacelList')->name('customer.pacel.update');

    // Users routes
    Route::resource('users', 'UsersController');

    // SMS routes
    Route::resource('sms', 'SmsController')->only(['index', 'edit', 'update']);

    // Staffs routes
    Route::group(['prefix' => 'staffs'], function(){
        Route::resource('dashboard','DashboardController');
    });

    Route::get('temp_pacel_delete/{id}/delete','DashboardController@temp_pacel_delete')->name('temp_pacel_delete');

    // Management routes (driver and car)
    Route::group(['prefix' => 'management'], function(){
        Route::resource('car','Management\CarController');
        Route::get('arrived_car','Management\CarController@arrived')->name('arrived_car');
        Route::post('arrived_car_store','Management\CarController@arrived_car_store')->name('arrived_car_store');
        Route::get('all_car','Management\CarController@all')->name('all_car');
        Route::get('active_car','Management\CarController@active')->name('active_car');
        Route::get('car_today2','Management\CarController@car_today')->name('car_today2');
        Route::get('car_today_routes/{id}/show','Management\CarController@car_today_routes')->name('car_today_routes');
        Route::get('car_pacel_detail/{id}/{date}/{date2}/index','Management\CarController@car_pacel_detail')->name('car_pacel_detail');
        Route::get('route_report/{id}/{date}/{date2}','Management\CarController@route_report')->name('route_report');
        Route::get('route_expenses/{driver_route_id}/show','Management\CarController@route_expenses')->name('route_expenses.show');
        Route::post('route_expenses/store','Management\CarController@route_expenses_store')->name('route_expenses.store');

        Route::get('findNameItem', 'DashboardController@findNameItem')->name('findNameItem');
        Route::get('findNameItem2', 'DashboardController@findNameItem2')->name('findNameItem2');

        Route::get('old_car','Management\CarController@old')->name('old_car');
        Route::get('car_routes','Management\CarController@car_routes')->name('car_routes');
        Route::get('car_pacel','Management\CarController@car_pacel')->name('car_pacel');
        Route::get('routes_overview','Management\CarController@routes_overview')->name('routes_overview');
        Route::get('search_store', 'Management\CarController@customer_history')->name('search_view');
        Route::get('delivery_note', 'Management\CarController@customer_history')->name('delivery_note');
        Route::get('search_store/{id}/show', 'Management\CarController@delivery_show')->name('delivery_show'); 
        Route::get('print_delivery_show/{id}/show', 'Management\CarController@print_delivery_show')->name('print_delivery_show'); 
        Route::get('print_pacel_registered/{id}/show', 'Management\CarController@print_pacel_registered')->name('print_pacel_registered'); 
        Route::get('pacel_reg/{customer_ID}/show', 'Management\CarController@pacel_reg')->name('pacel_reg'); 
        Route::get('pacel_delete/{id}/delete','Management\CarController@pacel_delete')->name('pacel_delete');
        Route::get('pacel_edit/{id}/edit','Management\CarController@pacel_edit')->name('pacel.edit');
        Route::post('pacel_update','Management\CarController@pacel_update')->name('pacel.update');
        Route::get('money_edit/{id}/edit','Management\CarController@money_edit')->name('money.edit');
        Route::post('money_update','Management\CarController@money_update')->name('money.update');

        // Route::get('gymkhana_test', 'Management\CarController@gymkhana_test')->name('gymkhana_test'); 
        // Route::get('gymkhana_test2', 'Management\CarController@gymkhana_test2')->name('gymkhana_test2'); 

        Route::get('car_manifest/{id}/{date}/{date2}/show', 'Management\CarController@car_manifest')->name('car_manifest'); 
        Route::get('car_manifest22/{id}/{date}/{date2}/show', 'Management\CarController@car_manifest22')->name('car_manifest22'); 
        Route::get('car_manifest23/{id}/{date}/{date2}/show', 'Management\CarController@car_manifest23')->name('car_manifest23'); 
        Route::get('car_manifest24/{id}/{date}/{date2}/show', 'Management\CarController@car_manifest24')->name('car_manifest24'); 
        Route::get('car_manifestAll/{id}/{date}/{date2}/show', 'Management\CarController@car_manifestAll')->name('car_manifestAll'); 
        Route::get('car_invoice/{id}/{date}/{date2}/show', 'Management\CarController@car_invoice')->name('car_invoice');
        Route::get('car_invoice22/{id}/{date}/{date2}/show', 'Management\CarController@car_invoice22')->name('car_invoice22');
        Route::get('car_invoice23/{id}/{date}/{date2}/show', 'Management\CarController@car_invoice23')->name('car_invoice23');
        Route::get('car_invoice24/{id}/{date}/{date2}/show', 'Management\CarController@car_invoice24')->name('car_invoice24');
        Route::get('car_invoice25/{id}/{date}/{date2}/show', 'Management\CarController@car_invoice25')->name('car_invoice25');
        Route::get('car_invoice26/{id}/{date}/{date2}/show', 'Management\CarController@car_invoice26')->name('car_invoice26');
        Route::get('car_invoice27/{id}/{date}/{date2}/show', 'Management\CarController@car_invoice27')->name('car_invoice27');
        Route::get('car_invoiceAll/{id}/{date}/{date2}/show', 'Management\CarController@car_invoiceAll')->name('car_invoiceAll');
        Route::get('car_single_invoice/{id}/{date}/{date2}/{pacel_id}/show', 'Management\CarController@car_single_invoice')->name('car_single_invoice'); 

        Route::get('search_user', 'Management\CarController@test')->name('search_user');
        Route::get('search', 'Management\CarController@search_store')->name('search_store');  
        Route::get('search/{id}/show', 'Management\CarController@customer_show')->name('customer_show'); 
        Route::get('pacel_store', 'Management\CarController@pacel_store')->name('pacel_store');
        Route::get('outstanding_debts', 'Management\CarController@outstanding_debts')->name('outstanding_debts');
        Route::get('pacel_show/{id}/show', 'Management\CarController@pacel_show')->name('pacel_show'); 
        Route::get('test1', 'Management\CarController@test1')->name('test1'); 
        Route::get('driver/approve/{id}', 'Management\DriverController@approve')->name('driver.approve');
        Route::resource('driver','Management\DriverController');               
    });  

    // Change password routes
    Route::get('change_password', 'HomeController@showChangePswd');
    Route::post('change_password_store', 'HomeController@changePswd')->name('changePswdPost');

    // 404 for undefined routes (must be last)
    Route::any('/{page?}',function(){
        return View::make('error-404');
    })->where('page','.*');
});










       



 








