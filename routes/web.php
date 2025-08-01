<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\ReportDataSaluranController;
use App\Http\Controllers\ReportLaporanPekerjaanRutinController;
use App\Http\Controllers\ReportLaporanPekerjaanSwakelolaController;
use App\Http\Controllers\ReportTanggapDaruratBencanaController;
use App\Http\Controllers\SosmedController;
use App\Http\Controllers\TanggapDaruratBencanaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Web\DataSaluranWebController;
use App\Http\Controllers\Web\LaporanPekerjaanRutinWebController;
use App\Http\Controllers\Web\LaporanSwakelolaWebController;
use App\Http\Controllers\Web\TanggapDaruratBencanaWebController;

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

Route::get('/privacy-policy', function () {
    return view('landing_page.index');
});
Route::get('/company-profile', function () {
    return view('company_profile.index');
});

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

    // Tanggap Darurat Bencana
    Route::resource('tanggap_darurat_bencana', TanggapDaruratBencanaWebController::class);
    Route::get('tanggap_darurat_bencana-data', [TanggapDaruratBencanaWebController::class, 'data'])->name('tanggap_darurat_bencana-data');

    Route::resource('laporan_pekerjaan_rutin', LaporanPekerjaanRutinWebController::class);
    Route::get('laporan_pekerjaan_rutin-data', [LaporanPekerjaanRutinWebController::class, 'data'])->name('laporan_pekerjaan_rutin-data');

    Route::resource('laporan_swakelola', LaporanSwakelolaWebController::class);
    Route::get('laporan_swakelola-data', [LaporanSwakelolaWebController::class, 'data'])->name('laporan_swakelola-data');

    Route::resource('data_saluran', DataSaluranWebController::class);
    Route::get('data_saluran-data', [DataSaluranWebController::class, 'data'])->name('data_saluran-data');


    // Route::delete('tanggap-darurat-bencana/delete/{id}', [TanggapDaruratBencanaWebController::class, 'delete'])->name('tanggap_darurat_bencana.index');

    Route::resource('users', UserController::class);
    Route::get('users-data', [UserController::class, 'data'])->name('users.data');

    Route::resource('position', PositionController::class);
    Route::get('position-data', [PositionController::class, 'data'])->name('position.data');

    Route::resource('sosmed', SosmedController::class);
    Route::get('sosmed-data', function(){return 'ok';})->name('sosmed.data');

    // [SosmedController::class, 'data']

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

        Route::get('laporan-pekerjaan-swakelola', [ReportLaporanPekerjaanSwakelolaController::class, 'index'])->name('report.laporan-pekerjaan-swakelola.index');
        Route::get('laporan-pekerjaan-swakelola/export', [ReportLaporanPekerjaanSwakelolaController::class, 'export'])->name('report.laporan-pekerjaan-swakelola.export');
        Route::get('laporan-pekerjaan-swakelola-data', [ReportLaporanPekerjaanSwakelolaController::class, 'data'])->name('report.laporan-pekerjaan-swakelola.data');
    });

});

