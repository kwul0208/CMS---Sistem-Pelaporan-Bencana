<?php

use App\Http\Controllers\LaporanPekerjaanRutinController;
use App\Http\Controllers\TanggapDaruratBencanaController;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });



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
