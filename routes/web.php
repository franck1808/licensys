<?php

use App\Http\Controllers\MainController;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [MainController::class, 'index'])->name('dashboard');
Route::get('/dashboard/customers', [MainController::class, 'allCustomers'])->name('allCustomers');
Route::post('/dashboard/customers/create', [MainController::class, 'createCustomer'])->name('createCustomer');
Route::get('/dashboard/customers/generate_api_key/{customer_id}', [MainController::class, 'generateAPIKey'])->name('generate_api_key');
Route::get('/dashboard/customers/apps', [MainController::class, 'allCustomersApps'])->name('allCustomerApps');
Route::post('/dashboard/customers/apps/create', [MainController::class, 'createCustomerApp'])->name('createCustomerApp');
Route::get('/dashboard/licenses', [MainController::class, 'allLicenseKeys'])->name('allLicenseKeys');
Route::post('/dashboard/licenses/create', [MainController::class, 'createLicenseKey'])->name('createLicenseKey');
