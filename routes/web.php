<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Auth;

// Guest/Public Routes
Route::middleware('guest')->group(function () {
    Route::get('/', function () {
        return view('auth.login');
    });
    
    Route::get('/home', function () {
        return view('auth.login');
    })->name('home');
});
Auth::routes();
Route::middleware('auth')->group(function () {
    // Common routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');

    // Admin routes with role middleware
    Route::prefix('admin')
         ->middleware('role:admin')
         ->group(function () {
             Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
             Route::get('/students', [AdminController::class, 'students'])->name('admin.students');
             Route::get('/students/{id}/courses', [AdminController::class, 'studentCourses'])
                  ->name('admin.student.courses');
           Route::delete('/students/{student}/courses/{course}', [AdminController::class, 'destroyCourse'])
            ->name('admin.students.courses.destroy');
    });

    // Student routes with role middleware
    Route::prefix('student')
         ->middleware('role:student')
         ->group(function () {
             Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('student.dashboard');
             Route::post('/enroll', [StudentController::class, 'enroll'])->name('student.enroll');
             Route::delete('/unenroll', [StudentController::class, 'unenroll'])->name('student.unenroll');
             Route::get('/enrollments', [StudentController::class, 'enrollments'])->name('student.enrollments');
       
    });
});