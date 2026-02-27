<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MyTrainingController;
use App\Http\Controllers\PdsController;
use App\Http\Controllers\PersonnelController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TrainingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Health check: hit this first to confirm the server responds (e.g. http://127.0.0.1:8000/ping)
Route::get('/ping', fn () => response('pong', 200)->header('Content-Type', 'text/plain'));

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    // Personnel (admin + sub-admin: list + view profile; admin + sub-admin: create/store)
    Route::get('/personnel', [PersonnelController::class, 'index'])->name('personnel.index')->middleware('role:admin,sub-admin');
    Route::get('/personnel/create', [AuthController::class, 'showCreatePersonnelForm'])->name('personnel.create')->middleware('role:admin,sub-admin');
    Route::post('/personnel', [AuthController::class, 'storePersonnel'])->name('personnel.store')->middleware('role:admin,sub-admin');
    Route::get('/personnel/{user}', [PersonnelController::class, 'show'])->name('personnel.show')->middleware('role:admin,sub-admin');
    Route::get('/personnel/{user}/pds', [PdsController::class, 'edit'])->name('personnel.pds.edit')->middleware('role:admin,sub-admin');
    Route::post('/personnel/{user}/pds', [PdsController::class, 'update'])->name('personnel.pds.update')->middleware('role:admin,sub-admin');
    Route::post('/personnel/{user}/pds/draft', [PdsController::class, 'draft'])->name('personnel.pds.draft')->middleware('role:admin,sub-admin');
    Route::get('/personnel/{user}/pds/print', [PdsController::class, 'print'])->name('personnel.pds.print')->middleware('role:admin,sub-admin');
    Route::get('/api/personnel/{user}/pds/importable-trainings', [PdsController::class, 'importableTrainings'])->name('api.personnel.pds.importable-trainings')->middleware('role:admin,sub-admin');
    Route::get('/api/personnel', [PersonnelController::class, 'list'])->name('api.personnel.list');
    Route::get('/api/personnel/{user}/trainings', [PersonnelController::class, 'trainings'])->name('api.personnel.trainings');

    // Trainings management (admin + sub-admin; sub-admin cannot delete)
    Route::get('/trainings', [TrainingController::class, 'manage'])->name('trainings.manage')->middleware('role:admin,sub-admin');
    Route::get('/api/trainings', [TrainingController::class, 'index'])->name('api.trainings.index')->middleware('role:admin,sub-admin');
    Route::get('/api/trainings/{training}', [TrainingController::class, 'show'])->name('api.trainings.show')->middleware('role:admin,sub-admin');
    Route::post('/api/trainings', [TrainingController::class, 'store'])->name('api.trainings.store')->middleware('role:admin,sub-admin');
    Route::put('/api/trainings/{training}', [TrainingController::class, 'update'])->name('api.trainings.update')->middleware('role:admin,sub-admin');
    Route::delete('/api/trainings/{training}', [TrainingController::class, 'destroy'])->name('api.trainings.destroy')->middleware('role:admin');
    Route::post('/api/trainings/{training}/attach', [TrainingController::class, 'attachUsers'])->name('api.trainings.attach')->middleware('role:admin,sub-admin');
    Route::post('/api/trainings/import', [TrainingController::class, 'import'])->name('api.trainings.import')->middleware('role:admin,sub-admin');
    Route::delete('/api/trainings/{training}/users/{user}', [TrainingController::class, 'detachUser'])->name('api.trainings.detach')->middleware('role:admin,sub-admin');
    Route::post('/api/personnel/{user}/trainings/detach-bulk', [TrainingController::class, 'detachBulk'])->name('api.personnel.trainings.detach-bulk')->middleware('role:admin,sub-admin');

    // My trainings (user self-service: add training/seminar to own record)
    Route::get('/api/my/record/trainings', [MyTrainingController::class, 'myRecord'])->name('api.my.record.trainings');
    Route::put('/api/my/record/trainings/{training}', [MyTrainingController::class, 'updatePivot'])->name('api.my.record.trainings.update');
    Route::delete('/api/my/record/trainings/{training}', [MyTrainingController::class, 'detach'])->name('api.my.record.trainings.detach');
    Route::get('/api/my/trainings', [MyTrainingController::class, 'index'])->name('api.my.trainings.index');
    Route::post('/api/my/trainings/attach', [MyTrainingController::class, 'attach'])->name('api.my.trainings.attach');
    Route::post('/api/my/trainings', [MyTrainingController::class, 'store'])->name('api.my.trainings.store');
    Route::post('/api/my/trainings/import', [MyTrainingController::class, 'import'])->name('api.my.trainings.import');

    // Profile (edit, change password) — all authenticated users
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/password', [ProfileController::class, 'showChangePasswordForm'])->name('profile.password');
    Route::post('/profile/password', [ProfileController::class, 'changePassword'])->name('profile.password.update');

    // Personal Data Sheet (own) — all authenticated users
    Route::get('/pds', [PdsController::class, 'edit'])->name('pds.edit');
    Route::post('/pds', [PdsController::class, 'update'])->name('pds.update');
    Route::post('/pds/draft', [PdsController::class, 'draft'])->name('pds.draft');
    Route::get('/pds/print', [PdsController::class, 'print'])->name('pds.print');

    // PDS importable trainings API (own)
    Route::get('/api/pds/importable-trainings', [PdsController::class, 'importableTrainings'])->name('api.pds.importable-trainings');

    // Reports
    Route::get('/reports/excel', [ReportController::class, 'excel'])->name('reports.excel');
    Route::get('/reports/pdf', [ReportController::class, 'pdf'])->name('reports.pdf');
    Route::get('/reports/pds-pdf', [ReportController::class, 'pdsPdf'])->name('reports.pds-pdf');
    Route::get('/reports/pds-excel', [ReportController::class, 'pdsExcel'])->name('reports.pds-excel');
    Route::get('/reports/pds-excel-pdf', [ReportController::class, 'pdsExcelPdf'])->name('reports.pds-excel-pdf');
});
