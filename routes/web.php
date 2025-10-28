<?php


use App\Http\Controllers\AuthController;
use App\Http\Controllers\DiskonController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

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

// Route::get('/', function () {
//     return view('welcome');
// });


// Route::middleware('auth')->group(function(){
//     Route::get('/',[SosmedController::class,'admin'])->name('admin');
// Route::post('export-youtube',[SosmedController::class,'exportYoutube'])->name('exportYoutube');
// Route::post('export-instagram',[SosmedController::class,'exportInstagram'])->name('exportInstagram');

// Route::get('/logout',[AuthController::class,'logout'])->name('logout');
// });

Route::middleware('auth')->group(function () {

    Route::middleware('hakakses:1')->group(function () {
        //laporan
        Route::get('/', [LaporanController::class, 'laporanPenjualan'])->name('laporanPenjualan');
        Route::get('detailLaporanPenjualan/{tgl}', [LaporanController::class, 'detailLaporanPenjualan'])->name('detailLaporanPenjualan');

        Route::get('laporanRefund', [LaporanController::class, 'laporanRefund'])->name('laporanRefund');
        Route::get('TerimaRefund/{id}', [LaporanController::class, 'TerimaRefund'])->name('TerimaRefund');
        Route::get('tolakRefund/{id}', [LaporanController::class, 'tolakRefund'])->name('tolakRefund');
        //laporan
        Route::get('user', [UserController::class, 'index'])->name('user');
        Route::get('get-data-user', [UserController::class, 'getDataUser'])->name('getDataUser');
        Route::post('edit-user', [UserController::class, 'editUser'])->name('editUser');
        Route::post('add-user', [UserController::class, 'addUser'])->name('addUser');

        //service
        Route::get('service', [ServiceController::class, 'index'])->name('service');
        Route::post('addService', [ServiceController::class, 'addService'])->name('addService');
        Route::patch('editService', [ServiceController::class, 'editService'])->name('editService');
        Route::get('deleteService/{id}', [ServiceController::class, 'deleteService'])->name('deleteService');
        //end service

        //karyawan
        Route::get('karyawan', [KaryawanController::class, 'index'])->name('karyawan');
        Route::post('addKaryawan', [KaryawanController::class, 'addKaryawan'])->name('addKaryawan');
        Route::patch('editKaryawan', [KaryawanController::class, 'editKaryawan'])->name('editKaryawan');
        Route::get('deleteKaryawan/{id}', [KaryawanController::class, 'deleteKaryawan'])->name('deleteKaryawan');
        //end karyawan

        //diskon
        Route::get('diskon', [DiskonController::class, 'index'])->name('diskon');
        Route::post('addDiskon', [DiskonController::class, 'addDiskon'])->name('addDiskon');
        Route::patch('editDiskon', [DiskonController::class, 'editDiskon'])->name('editDiskon');
        //end diskon
    });


    //block
    Route::get('forbidden-access', [AuthController::class, 'block'])->name('block');
    //endblock
    Route::get('ganti-password', [UserController::class, 'gantiPassword'])->name('gantiPassword');
    Route::post('edit-password', [UserController::class, 'editPassword'])->name('editPassword');



    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('non-active', [AuthController::class, 'nonActive'])->name('nonActive');
});




Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'login_page'])->name('loginPage');
    Route::post('login', [AuthController::class, 'login'])->name('login');
});
