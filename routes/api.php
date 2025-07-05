<?php

use App\Http\Controllers\AuthApiController;
use App\Http\Controllers\DataSaluranController;
use App\Http\Controllers\LaporanPekerjaanRutinController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\TanggapDaruratBencanaController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/login', [AuthApiController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthApiController::class, 'logout']);
    
    // Profile
    Route::get('profile/{id}', [UserController::class, 'getAPIProfile']);
    Route::post('profile/update/{id}', [UserController::class, 'updateAPIProfile']);

    // ORG
    Route::get('structure-org', [PositionController::class, 'getStructureOrg']);


    // Tanggap Darurat Bencana
    Route::get('tanggap-darurat-bencana', [TanggapDaruratBencanaController::class, 'index']);
    Route::get('tanggap-darurat-bencana/show/{id}', [TanggapDaruratBencanaController::class, 'show']);
    Route::post('tanggap-darurat-bencana/store', [TanggapDaruratBencanaController::class, 'store']);
    Route::post('tanggap-darurat-bencana/update', [TanggapDaruratBencanaController::class, 'update']);
    Route::delete('tanggap-darurat-bencana/delete/{id}', [TanggapDaruratBencanaController::class, 'delete']);

    // Laporan Pekerjaan Rutin
    Route::get('laporan-pekerjaan-rutin', [LaporanPekerjaanRutinController::class, 'index']);
    Route::get('laporan-pekerjaan-rutin/show/{id}', [LaporanPekerjaanRutinController::class, 'show']);
    Route::post('laporan-pekerjaan-rutin/store', [LaporanPekerjaanRutinController::class, 'store']);
    Route::post('laporan-pekerjaan-rutin/update', [LaporanPekerjaanRutinController::class, 'update']);
    Route::delete('laporan-pekerjaan-rutin/delete/{id}', [LaporanPekerjaanRutinController::class, 'delete']);

    // Data Saluran
    Route::get('data-saluran', [DataSaluranController::class, 'index']);
    Route::get('data-saluran/show/{id}', [DataSaluranController::class, 'show']);
    Route::post('data-saluran/store', [DataSaluranController::class, 'store']);
    Route::post('data-saluran/update', [DataSaluranController::class, 'update']);
    Route::delete('data-saluran/delete/{id}', [DataSaluranController::class, 'delete']);
});

Route::get('/testing', [TanggapDaruratBencanaController::class, 'testing']);
