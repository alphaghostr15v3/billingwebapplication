<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\SuperAdmin\DashboardController as SuperDashboard;
use App\Http\Controllers\SuperAdmin\BusinessController as SuperBusiness;
use App\Http\Controllers\Business\DashboardController as BusinessDashboard;

Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/', function () {
    return redirect()->route('login');
});

Route::prefix('super-admin')->name('super-admin.')->group(function () {
    Route::get('/dashboard', [SuperDashboard::class, 'index'])->name('dashboard');
    Route::resource('subscriptions', \App\Http\Controllers\SuperAdmin\SubscriptionController::class);
    Route::patch('businesses/{business}/toggle-status', [SuperBusiness::class, 'toggleStatus'])->name('businesses.toggle-status');
    Route::resource('businesses', SuperBusiness::class);
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
});

Route::prefix('business')->name('business.')->middleware(['auth', 'tenant'])->group(function () {
    Route::get('/dashboard', [BusinessDashboard::class, 'index'])->name('dashboard');
    
    // Subscriptions
    Route::get('/subscriptions', [\App\Http\Controllers\Business\PaymentController::class, 'index'])->name('subscriptions.index');
    Route::post('/subscriptions/initiate', [\App\Http\Controllers\Business\PaymentController::class, 'initiatePayment'])->name('subscriptions.initiate');
    Route::post('/subscriptions/verify', [\App\Http\Controllers\Business\PaymentController::class, 'verifyPayment'])->name('subscriptions.verify');
    
    Route::resource('customers', \App\Http\Controllers\Business\CustomerController::class);
    Route::resource('products', \App\Http\Controllers\Business\ProductController::class);
    Route::post('categories', [\App\Http\Controllers\Business\ProductController::class, 'storeCategory'])->name('categories.store');
    Route::resource('invoices', \App\Http\Controllers\Business\InvoiceController::class);
    Route::get('/invoices/{invoice}/download', [\App\Http\Controllers\Business\InvoiceController::class, 'downloadPDF'])->name('invoices.download');
    Route::resource('expenses', \App\Http\Controllers\Business\ExpenseController::class);
    Route::get('/reports', [\App\Http\Controllers\Business\ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [\App\Http\Controllers\Business\ReportController::class, 'export'])->name('reports.export');
    Route::get('/reports/gst', [\App\Http\Controllers\Business\GSTReportController::class, 'index'])->name('reports.gst');
    Route::get('/reports/gst/export', [\App\Http\Controllers\Business\GSTReportController::class, 'export'])->name('reports.gst.export');
    Route::get('/reports/gst/pdf', [\App\Http\Controllers\Business\GSTReportController::class, 'downloadPDF'])->name('reports.gst.pdf');
    
    // GSTR-1
    Route::get('/reports/gstr1', [\App\Http\Controllers\Business\GSTR1Controller::class, 'index'])->name('reports.gstr1');
    Route::get('/reports/gstr1/b2b', [\App\Http\Controllers\Business\GSTR1Controller::class, 'exportB2B'])->name('reports.gstr1.b2b');
    Route::get('/reports/gstr1/b2c', [\App\Http\Controllers\Business\GSTR1Controller::class, 'exportB2C'])->name('reports.gstr1.b2c');
    
    // Profile
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/business', [\App\Http\Controllers\ProfileController::class, 'updateBusiness'])->name('profile.updateBusiness');
});
