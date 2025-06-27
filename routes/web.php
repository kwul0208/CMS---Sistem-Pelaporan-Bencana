<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\ReportLaporanPekerjaanRutinController;
use App\Http\Controllers\ReportTanggapDaruratBencanaController;
use App\Http\Controllers\TanggapDaruratBencanaController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', [DashboardController::class, 'index'])->name('home');

Route::resource('users', UserController::class);
Route::get('users-data', [UserController::class, 'data'])->name('users.data');

Route::resource('position', PositionController::class);
Route::get('position-data', [PositionController::class, 'data'])->name('position.data');
Route::get('api/structure-org', [PositionController::class, 'getStructureOrg']);

Route::prefix('report')->group(function() {
    Route::get('tanggap-darurat-bencana', [ReportTanggapDaruratBencanaController::class, 'index'])->name('report.tanggap-darurat-bencana.index');
    Route::get('tanggap-darurat-bencana-data', [ReportTanggapDaruratBencanaController::class, 'data'])->name('report.tanggap-darurat-bencana.data');

    Route::get('laporan-pekerjaan-rutin', [ReportLaporanPekerjaanRutinController::class, 'index'])->name('report.laporan-pekerjaan-rutin.index');
    Route::get('laporan-pekerjaan-rutin-data', [ReportLaporanPekerjaanRutinController::class, 'data'])->name('report.laporan-pekerjaan-rutin.data');
});




