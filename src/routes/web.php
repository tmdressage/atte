<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\WorktimeController;
use App\Http\Controllers\ResttimeController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\EmailVerificationController;
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

Route::controller(EmailVerificationController::class)
    ->prefix('email')->name('verification.')->group(function () {
        Route::get('/verify', 'verify')->name('notice');
        Route::post('/notification', 'notification')
            ->middleware('throttle:6,1')->name('send');
        Route::get('/verification/{id}/{hash}', 'verification')
            ->middleware(['signed', 'throttle:6,1'])->name('verify');
    });


Route::middleware('web', 'verified', 'auth')->group(function () {

    Route::get('/', [AuthController::class, 'home']);

    Route::get('/worktime-punchIn', [WorktimeController::class, 'punchIn'])->name('worktime-punchIn');
    Route::post('/worktime-punchIn', [WorktimeController::class, 'punchIn'])->name('worktime-punchIn');

    Route::get('/worktime-punchOut', [WorktimeController::class, 'punchOut'])->name('worktime-punchOut'); 
    Route::post('/worktime-punchOut', [WorktimeController::class, 'punchOut'])->name('worktime-punchOut');

    Route::get('/resttime-punchIn', [ResttimeController::class, 'punchIn'])->name('resttime-punchIn');
    Route::post('/resttime-punchIn', [ResttimeController::class, 'punchIn'])->name('resttime-punchIn');  
    
    Route::get('/resttime-punchOut', [ResttimeController::class, 'punchOut'])->name('resttime-punchOut');
    Route::post('/resttime-punchOut', [ResttimeController::class, 'punchOut'])->name('resttime-punchOut');
    
    Route::get('/attendance', [AttendanceController::class, 'attendance']);
});