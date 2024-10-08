<?php

use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\StripeCustomersController;
use App\Http\Controllers\StripeCardPaymentMethodController;
use App\Http\Controllers\StripeBankPaymentMethodController;
use App\Http\Controllers\InstantBankVerificationController;
use App\Http\Controllers\SubscriptionController;
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
Route::get('udelete', [StripeCustomersController::class, 'destroy'])->middleware(['auth', 'verified'])->name('udelete');
// Update Customer
Route::get('uupdate', [StripeCustomersController::class, 'update'])->middleware(['auth', 'verified'])->name('uupdate');
// Retrive Customer
Route::get('ushow', [StripeCustomersController::class, 'show'])->middleware(['auth', 'verified'])->name('ushow');




Route::get('/cpm', [StripeCardPaymentMethodController::class, 'index'])->middleware(['auth', 'verified'])->name('cpm');
Route::get('pmshow', [StripeCardPaymentMethodController::class, 'show'])->middleware(['auth', 'verified'])->name('pmshow');
Route::get('pmdelete', [StripeCardPaymentMethodController::class, 'destroy'])->middleware(['auth', 'verified'])->name('pmdelete');


Route::get('/bpm', [StripeBankPaymentMethodController::class, 'index'])->middleware(['auth', 'verified'])->name('bpm');
Route::get('/mbpm', [InstantBankVerificationController::class, 'index'])->middleware(['auth', 'verified'])->name('mbpm');



Route::get('/csub', [SubscriptionController::class, 'index'])->middleware(['auth', 'verified'])->name('csub');
Route::get('/rsub', [SubscriptionController::class, 'resume'])->middleware(['auth', 'verified'])->name('rsub');
Route::get('/resub', [SubscriptionController::class, 'retryPayment'])->middleware(['auth', 'verified'])->name('resub');
Route::get('/usub', [SubscriptionController::class, 'update'])->middleware(['auth', 'verified'])->name('usub');
Route::get('/dsub', [SubscriptionController::class, 'duplicate'])->middleware(['auth', 'verified'])->name('dsub');

require __DIR__.'/auth.php';
