<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Akun\AkunController;
use App\Http\Controllers\User\AuthController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Kendaraan\KendaraanController;
use App\Http\Controllers\Pendapatan\PendapatanController;
use App\Http\Controllers\Pengeluaran\PengeluaranController;
use App\Http\Controllers\Pendapatan\DetailPendapatanController;
use App\Http\Controllers\Pengeluaran\DetailPengeluaranController;

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

    /* PENDAPATAN */
    Route::controller(PendapatanController::class)->group(function(){
        Route::get('/pendapatan', 'index')->name('pendapatan.index');
        Route::post('/pendapatan', 'store')->name('pendapatan.store');
        Route::get('/pendapatan/{id}', 'show')->name('pendapatan.show');
        Route::put('/pendapatan/{id}', 'update')->name('pendapatan.update');
        Route::delete('/pendapatan/{id}', 'destroy')->name('pendapatan.destroy');
        Route::delete('/pendapatan', 'destroyMultiple')->name('pendapatan.destroyMultiple');
    });

    /* DETAIL PENDAPATAN */
    Route::controller(DetailPendapatanController::class)->group(function(){
        Route::get('/detail-pendapatan', 'index')->name('detail-pendapatan.index');
        Route::post('/detail-pendapatan', 'store')->name('detail-pendapatan.store');
        Route::get('/detail-pendapatan/{id}', 'show')->name('detail-pendapatan.show');
        Route::put('/detail-pendapatan/{id}', 'update')->name('detail-pendapatan.update');
        Route::delete('/detail-pendapatan/{id}', 'destroy')->name('detail-pendapatan.destroy');
        Route::delete('/detail-pendapatan', 'destroyMultiple')->name('detail-pendapatan.destroyMultiple');
    });

    /* PENGELUARAN */
    Route::controller(PengeluaranController::class)->group(function(){
        Route::get('/pengeluaran', 'index')->name('pengeluaran.index');
        Route::post('/pengeluaran', 'store')->name('pengeluaran.store');
        Route::get('/pengeluaran/{id}', 'show')->name('pengeluaran.show');
        Route::put('/pengeluaran/{id}', 'update')->name('pengeluaran.update');
        Route::delete('/pengeluaran/{id}', 'destroy')->name('pengeluaran.destroy');
        Route::delete('/pengeluaran', 'destroyMultiple')->name('pengeluaran.destroyMultiple');
    });

    /* DETAIL PENGELUARAN */
    Route::controller(DetailPengeluaranController::class)->group(function(){
        Route::get('/detail-pengeluaran', 'index')->name('detail-pengeluaran.index');
        Route::post('/detail-pengeluaran', 'store')->name('detail-pengeluaran.store');
        Route::get('/detail-pengeluaran/{id}', 'show')->name('detail-pengeluaran.show');
        Route::put('/detail-pengeluaran/{id}', 'update')->name('detail-pengeluaran.update');
        Route::delete('/detail-pengeluaran/{id}', 'destroy')->name('detail-pengeluaran.destroy');
        Route::delete('/detail-pengeluaran', 'destroyMultiple')->name('detail-pengeluaran.destroyMultiple');
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
