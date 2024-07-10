<?php

use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\StripeCustomersController;
use App\Http\Controllers\StripePaymentMethodController;
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

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')->middleware(['auth', 'verified'])->name('dashboard');

Route::view('pricing', 'pricing')->middleware(['auth', 'verified'])->name('pricing');

Route::get('checkout/{plan?}', CheckoutController::class)->middleware(['auth', 'verified'])->name('checkout');

Route::view('success', 'success')->middleware(['auth', 'verified'])->name('success');

Route::view('profile', 'profile')->middleware(['auth'])->name('profile');

// Create Customer 
Route::get('ucreate', [StripeCustomersController::class, 'create'])->middleware(['auth', 'verified'])->name('ucreate');
// Delete Customer 
Route::get('udelete', [StripeCustomersController::class, 'destroy'])->middleware(['auth', 'verified'])->name('ucreate');
// Update Customer 
Route::get('uupdate', [StripeCustomersController::class, 'update'])->middleware(['auth', 'verified'])->name('ucreate');
// Retrive Customer 
Route::get('ushow', [StripeCustomersController::class, 'show'])->middleware(['auth', 'verified'])->name('ucreate');

Route::get('/payment-method', [StripePaymentMethodController::class, 'index'])->middleware(['auth', 'verified'])->name('payment.method');
// Route::post('/update-payment-method', [StripePaymentMethodController::class, 'index'])->name('update.payment.method');



require __DIR__.'/auth.php';
