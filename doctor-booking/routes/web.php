<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\AdminController;

Route::get('/', [BookingController::class, 'index']);
Route::post('/book', [BookingController::class, 'store']);
Route::get('/booked-slots', [BookingController::class, 'bookedSlots']);

Route::get('/admin', [AdminController::class, 'index'])->name('admin');
Route::post('/admin/login', [AdminController::class, 'login'])->name('admin.login');
Route::post('/admin/logout', [AdminController::class, 'logout'])->name('admin.logout');
Route::delete('/admin/{id}', [AdminController::class, 'cancel'])
    ->middleware('auth')
    ->name('admin.cancel');
Route::get('/admin-data', [AdminController::class, 'adminData'])->middleware('auth');
Route::get('/admin/appointments-data', [AdminController::class, 'appointmentsData'])
    ->middleware('auth');