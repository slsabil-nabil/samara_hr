<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\OfficialDocumentController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\PenaltyController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TrainingController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'authenticate'])->name('login.store');
});

Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::redirect('/', '/dashboard');
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::get('/employees/import', [EmployeeController::class, 'importForm'])
        ->name('employees.import.form');
    Route::post('/employees/import', [EmployeeController::class, 'import'])
        ->name('employees.import.store');

    Route::resource('employees', EmployeeController::class)->except('show');
    Route::resource('leaves', LeaveRequestController::class)->except('show');
    Route::resource('loans', LoanController::class)->except('show');
    Route::resource('documents', OfficialDocumentController::class)->except('show');
    Route::resource('penalties', PenaltyController::class)->except('show');
    Route::resource('attendances', AttendanceController::class)->except('show');
    Route::resource('trainings', TrainingController::class)->except('show');

    Route::get('/payrolls', [PayrollController::class, 'index'])->name('payrolls.index');
    Route::post('/payrolls/generate', [PayrollController::class, 'generate'])->name('payrolls.generate');
    Route::patch('/payrolls/{payroll}/pay', [PayrollController::class, 'pay'])->name('payrolls.pay');

    Route::get('/reports', ReportController::class)->name('reports');
    Route::get('/audit-logs', AuditLogController::class)->name('audit.index');
});