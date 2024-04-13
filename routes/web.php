<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BillController;
use App\Http\Controllers\BillInformationController;
use App\Http\Controllers\ClassroomController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MyBillController;
use App\Http\Controllers\MyPaymentController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentParentController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'authenticate'])->name('login.authenticate');
});

Route::prefix('dashboard')->as('dashboard.')->middleware('auth')->group(function () {
    Route::middleware('role:admin|student|student_parent')->group(function () {
        Route::get('/index', [DashboardController::class, 'index'])->name('index');
        Route::get('/profile', [DashboardController::class, 'profile'])->name('profile');
        Route::post('/change-password', [DashboardController::class, 'changePassword'])->name('change-password');
    });

    Route::middleware('role:admin')->group(function () {
        Route::resource('/admins', AdminController::class)->except('show');
        Route::resource('/classrooms', ClassroomController::class)->except('show');
        Route::resource('/student-parents', StudentParentController::class)->except('show');

        Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
        Route::post('/payments/{payment}/reject', [PaymentController::class, 'reject'])->name('payments.reject');
        Route::post('/payments/{payment}/accept', [PaymentController::class, 'accept'])->name('payments.accept');

        Route::resource('/students', StudentController::class)->except('show');
        Route::get('/students/{student}/bills', [BillController::class, 'index'])->name('students.bills.index');
    });

    Route::middleware('role:student_parent')->group(function () {
        Route::get('/my-bills', [MyBillController::class, 'index'])->name('my-bills.index');
        Route::resource('/my-bills/{bill}/payments', MyPaymentController::class, ['as' => 'my-bills']);
    });

    Route::middleware('role:student')->group(function () {
        Route::get('bill-informations', [BillInformationController::class, 'index'])->name('bill-informations.index');
    });
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
