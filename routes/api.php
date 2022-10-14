<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Akun\AkunController;
use App\Http\Controllers\User\AuthController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Kendaraan\KendaraanController;

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

/* REGISTER & LOGIN (AUTH) */
Route::controller(AuthController::class)->group(function(){
    Route::post('/auth/register', 'register')->name('auth.register');
    Route::post('/auth/login', 'login')->name('auth.login');
});

Route::middleware(['auth:api'])->group(function(){
    /* BAGAN AKUN */
    Route::controller(AkunController::class)->group(function(){
        Route::get('/akun/options', 'fetchDataOptions')->name('akun.fetchDataOptions');
        Route::get('/akun', 'index')->name('akun.index');
        Route::post('/akun', 'store')->name('akun.store');
        Route::get('/akun/{id}', 'show')->name('akun.show');
        Route::put('/akun/{id}', 'update')->name('akun.update');
        Route::delete('/akun/{id}', 'destroy')->name('akun.destroy');
        Route::delete('/akun', 'destroyMultiple')->name('akun.destroyMultiple');
    });

    /* KENDARAAN */
    Route::controller(KendaraanController::class)->group(function(){
        Route::get('/kendaraan/options', 'fetchDataOptions')->name('kendaraan.fetchDataOptions');
        Route::get('/kendaraan', 'index')->name('kendaraan.index');
        Route::post('/kendaraan', 'store')->name('kendaraan.store');
        Route::get('/kendaraan/{id}', 'show')->name('kendaraan.show');
        Route::put('/kendaraan/{id}', 'update')->name('kendaraan.update');
        Route::delete('/kendaraan/{id}', 'destroy')->name('kendaraan.destroy');
        Route::delete('/kendaraan', 'destroyMultiple')->name('kendaraan.destroyMultiple');
    });

    /* USERS & LOGOUT */
    Route::controller(UserController::class)->group(function(){
        Route::delete('/users', 'destroyMultiple')->name('users.destroyMultiple');
        Route::put('/users/profile', 'updateProfile')->name('users.updateProfile');
        Route::put('/users/password', 'changePassword')->name('users.changePassword');
    });
    Route::apiResource('users', UserController::class);
    Route::get('/auth/logout', [AuthController::class, 'logout'])->name('auth.logout');
});
