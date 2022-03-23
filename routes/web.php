<?php

use Illuminate\Support\Facades\Route;
use LaravelDaily\LaravelCharts\Classes\LaravelChart;
use App\Models\Checkin;
use App\Models\Borrow;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

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

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    // $chart_options = [
    //     'chart_title' => 'Check in users daily',
    //     'report_type' => 'group_by_date',
    //     'model' => 'App\Models\Checkin',
    //     'group_by_field' => 'created_at',
    //     'group_by_period' => 'day',
    //     'chart_type' => 'pie',
    //     'filter_field' => 'created_at',
    //     'filter_days' => 30 // show only transactions for last 30 days
    // ];

    // $chart1 = new LaravelChart($chart_options);
    
    $usersss = Auth::user();
    $userrole = User::with('roles')->where('name', $usersss->name)->get();

    foreach($userrole as $users){
        foreach($users->roles as $role){    
            $setting = $role->title; 
            if( $setting == 'Admin'){
                $borrows = Borrow::where('approval', 'pending')->get();
                return view('dashboard',compact('borrows'));
            }else{
                $borrows = Borrow::where('approval', 'pending')->where('borrow', $usersss->name)->get();
                return view('dashboard',compact('borrows'));
            }
        }
    }    
    
})->name('dashboard');

Route::group(['middleware' => 'auth'], function () {
    Route::resource('guest', \App\Http\Controllers\GuestController::class);
    Route::resource('inventory', \App\Http\Controllers\InventoryController::class);
    Route::resource('checkin', \App\Http\Controllers\CheckinController::class);
    Route::get('itemcheckin', [\App\Http\Controllers\CheckinController::class, 'itemcheckin'])->name('itemcheckin');
    Route::get('alllistpage/{id}', [\App\Http\Controllers\InventoryController::class, 'alllistpage']);
    Route::get('borrow/{id}', [\App\Http\Controllers\InventoryController::class, 'borrow']);
    Route::post('book/{id}', [\App\Http\Controllers\InventoryController::class, 'book'])->name('book');
    Route::get('return/{id}', [\App\Http\Controllers\InventoryController::class, 'return'])->name('return');
    Route::get('approve/{id}', [\App\Http\Controllers\InventoryController::class, 'approve'])->name('approve');
    Route::get('decline/{id}', [\App\Http\Controllers\InventoryController::class, 'decline'])->name('decline');
    Route::resource('users', \App\Http\Controllers\UsersController::class);

    Route::get('/clearsession', [\App\Http\Controllers\CheckinController::class, 'clearSession']);
    Route::get('/checkstatusitem/{staffid}', [\App\Http\Controllers\CheckinController::class, 'checkStatusItem']);
    Route::get('/booking/{itemid}', [\App\Http\Controllers\CheckinController::class, 'booking']);

    Route::get('/checkstatus/{staffid}', [\App\Http\Controllers\CheckinController::class, 'checkStatus']);
    Route::get('/checkin/{staffid}', [\App\Http\Controllers\CheckinController::class, 'checkIn']);
    Route::get('/checkout/{staffid}', [\App\Http\Controllers\CheckinController::class, 'checkOut']);
    Route::get('/guestcheckin/{guestid}', [\App\Http\Controllers\CheckinController::class, 'guestCheckIn']);
    Route::get('/guestcheckout/{guestid}', [\App\Http\Controllers\CheckinController::class, 'guestCheckOut']);

    Route::get('/export', [\App\Http\Controllers\ExportController::class, 'export'])->name('export');
});