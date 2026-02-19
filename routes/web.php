<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ClientExportController;
use App\Http\Controllers\CourseCategoryController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CourseOpeningController;
use App\Http\Controllers\CoursePaymentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ServiceCategoryController;
use App\Http\Controllers\ServiceController;
use Illuminate\Support\Facades\Route;

// ── Públicas ──────────────────────────────────────────────────────────────────

Route::get('/', fn() => redirect()->route('login'));
Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout',[AuthController::class, 'logout'])->name('logout');

// ── Autenticadas ──────────────────────────────────────────────────────────────

Route::middleware('auth')->group(function () {

    Route::get('/dashboard', fn() => view('dashboard'))->name('dashboard');

    // ── Citas ─────────────────────────────────────────────────────────────────
    Route::resource('appointments', AppointmentController::class)
        ->only(['index', 'store', 'update', 'destroy']);

    // ── Locales ───────────────────────────────────────────────────────────────
    Route::prefix('branches')->name('branches.')->group(function () {
        Route::get('/',                  [BranchController::class, 'index'])->name('index');
        Route::get('/create',            [BranchController::class, 'create'])->name('create');
        Route::post('/',                 [BranchController::class, 'store'])->name('store');
        Route::get('/{branch}',          [BranchController::class, 'show'])->name('show');
        Route::get('/{branch}/edit',     [BranchController::class, 'edit'])->name('edit');
        Route::put('/{branch}',          [BranchController::class, 'update'])->name('update');
        Route::delete('/{branch}',       [BranchController::class, 'destroy'])->name('destroy');
        Route::patch('/{branch}/toggle', [BranchController::class, 'toggleActive'])->name('toggle');
    });

    // ── Servicios ─────────────────────────────────────────────────────────────
    Route::prefix('service-categories')->name('service-categories.')->group(function () {
        Route::get('/',                       [ServiceCategoryController::class, 'index'])->name('index');
        Route::get('/create',                 [ServiceCategoryController::class, 'create'])->name('create');
        Route::post('/',                      [ServiceCategoryController::class, 'store'])->name('store');
        Route::post('/ajax',                  [ServiceCategoryController::class, 'storeAjax'])->name('store-ajax');
        Route::get('/{serviceCategory}/edit', [ServiceCategoryController::class, 'edit'])->name('edit');
        Route::put('/{serviceCategory}',      [ServiceCategoryController::class, 'update'])->name('update');
        Route::delete('/{serviceCategory}',   [ServiceCategoryController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('services')->name('services.')->group(function () {
        Route::get('/',                         [ServiceController::class, 'index'])->name('index');
        Route::get('/create',                   [ServiceController::class, 'create'])->name('create');
        Route::post('/',                        [ServiceController::class, 'store'])->name('store');
        Route::get('/{service}',                [ServiceController::class, 'show'])->name('show');
        Route::get('/{service}/edit',           [ServiceController::class, 'edit'])->name('edit');
        Route::put('/{service}',                [ServiceController::class, 'update'])->name('update');
        Route::delete('/{service}',             [ServiceController::class, 'destroy'])->name('destroy');
        Route::delete('/images/{image}',        [ServiceController::class, 'destroyImage'])->name('images.destroy');
        Route::patch('/images/{image}/primary', [ServiceController::class, 'setPrimaryImage'])->name('images.primary');
    });

    // ── Cursos ────────────────────────────────────────────────────────────────
    Route::prefix('course-categories')->name('course-categories.')->group(function () {
        Route::get('/',                      [CourseCategoryController::class, 'index'])->name('index');
        Route::get('/create',                [CourseCategoryController::class, 'create'])->name('create');
        Route::post('/',                     [CourseCategoryController::class, 'store'])->name('store');
        Route::post('/ajax',                 [CourseCategoryController::class, 'storeAjax'])->name('store-ajax');
        Route::get('/{courseCategory}/edit', [CourseCategoryController::class, 'edit'])->name('edit');
        Route::put('/{courseCategory}',      [CourseCategoryController::class, 'update'])->name('update');
        Route::delete('/{courseCategory}',   [CourseCategoryController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('courses')->name('courses.')->group(function () {
        Route::get('/',              [CourseController::class, 'index'])->name('index');
        Route::get('/create',        [CourseController::class, 'create'])->name('create');
        Route::post('/',             [CourseController::class, 'store'])->name('store');
        Route::get('/{course}',      [CourseController::class, 'show'])->name('show');
        Route::get('/{course}/edit', [CourseController::class, 'edit'])->name('edit');
        Route::put('/{course}',      [CourseController::class, 'update'])->name('update');
        Route::delete('/{course}',   [CourseController::class, 'destroy'])->name('destroy');
    });

    // ── Aperturas de curso ────────────────────────────────────────────────────
    // Rutas estáticas ANTES del resource para evitar conflictos con parámetros
    Route::post(
        'course-openings/sessions/{session}/attendance',
        [CourseOpeningController::class, 'saveAttendance']
    )->name('course-openings.attendance');

    Route::resource('course-openings', CourseOpeningController::class);

    // ── Pagos de apertura ─────────────────────────────────────────────────────
    Route::prefix('course-openings/{courseOpening}/payments')
        ->name('course-openings.payments.')
        ->group(function () {
            Route::get('/',          [CoursePaymentController::class, 'index'])->name('index');
            Route::post('/generate', [CoursePaymentController::class, 'generate'])->name('generate');
            Route::post('/store',    [CoursePaymentController::class, 'store'])->name('store');
        });

    // Acciones sobre una cuota individual
    // Nota: el prefijo no incluye {courseOpening} porque el payment ya conoce su apertura
    Route::prefix('course-openings/payments/{payment}')
        ->name('course-openings.payments.')
        ->group(function () {
            Route::post('/pay',    [CoursePaymentController::class, 'pay'])->name('pay');
            Route::patch('/status',[CoursePaymentController::class, 'updateStatus'])->name('update-status');
            Route::delete('/',     [CoursePaymentController::class, 'destroy'])->name('destroy');
        });

    // ── Clientes ──────────────────────────────────────────────────────────────
    // Export ANTES del resource para evitar conflicto con {client}
    Route::get('clients/export', [ClientExportController::class, 'export'])->name('clients.export');
    Route::resource('clients', ClientController::class);

    // ── Empleados ─────────────────────────────────────────────────────────────
    Route::prefix('employees')->name('employees.')->group(function () {
        Route::get('/',                     [EmployeeController::class, 'index'])->name('index');
        Route::get('/create',               [EmployeeController::class, 'create'])->name('create');
        Route::post('/',                    [EmployeeController::class, 'store'])->name('store');
        Route::get('/{employee}',           [EmployeeController::class, 'show'])->name('show');
        Route::get('/{employee}/edit',      [EmployeeController::class, 'edit'])->name('edit');
        Route::put('/{employee}',           [EmployeeController::class, 'update'])->name('update');
        Route::delete('/{employee}',        [EmployeeController::class, 'destroy'])->name('destroy');
        Route::patch('/{employee}/toggle',  [EmployeeController::class, 'toggleActive'])->name('toggle');
        Route::get('/{employee}/schedules', [EmployeeController::class, 'schedules'])->name('schedules');
        Route::post('/{employee}/schedules',[EmployeeController::class, 'updateSchedules'])->name('schedules.update');
    });

});