<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\ReportDataSaluranController;
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

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/optimize-clear', function() {
        \Artisan::call('optimize:clear');
        return 'Cache cleared';
    })->name('optimize.clear');

    Route::get('/storage-link', function() {
        \Artisan::call('storage:link');
        return 'Storage linked';
    })->name('storage.link');

Route::middleware('auth')->group(function() {
    Route::get('/', [DashboardController::class, 'index'])->name('home');

    Route::resource('users', UserController::class);
    Route::get('users-data', [UserController::class, 'data'])->name('users.data');

    Route::resource('position', PositionController::class);
    Route::get('position-data', [PositionController::class, 'data'])->name('position.data');

    Route::prefix('report')->group(function() {
        Route::get('tanggap-darurat-bencana', [ReportTanggapDaruratBencanaController::class, 'index'])->name('report.tanggap-darurat-bencana.index');
        Route::get('tanggap-darurat-bencana/export', [ReportTanggapDaruratBencanaController::class, 'export'])->name('report.tanggap-darurat-bencana.export');
        Route::get('tanggap-darurat-bencana-data', [ReportTanggapDaruratBencanaController::class, 'data'])->name('report.tanggap-darurat-bencana.data');
        
        Route::get('laporan-pekerjaan-rutin', [ReportLaporanPekerjaanRutinController::class, 'index'])->name('report.laporan-pekerjaan-rutin.index');
        Route::get('laporan-pekerjaan-rutin/export', [ReportLaporanPekerjaanRutinController::class, 'export'])->name('report.laporan-pekerjaan-rutin.export');
        Route::get('laporan-pekerjaan-rutin-data', [ReportLaporanPekerjaanRutinController::class, 'data'])->name('report.laporan-pekerjaan-rutin.data');

        Route::get('data-saluran', [ReportDataSaluranController::class, 'index'])->name('report.data-saluran.index');
        Route::get('data-saluran/export', [ReportDataSaluranController::class, 'export'])->name('report.data-saluran.export');
        Route::get('data-saluran-data', [ReportDataSaluranController::class, 'data'])->name('report.data-saluran.data');
    });

});

