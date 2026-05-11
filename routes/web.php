<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CandidateController;
use App\Http\Controllers\Admin\OfferLetterController;
use App\Http\Controllers\Admin\SettingController;

/*
|--------------------------------------------------------------------------
| Admin Auth Routes
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return redirect()->route('admin.login');
});
Route::get('/admin/login', [AuthController::class, 'loginForm'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'login'])->name('admin.login.submit');
Route::get('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout');


Route::prefix('admin')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    // Settings
    Route::get('/settings', [SettingController::class, 'index'])->name('admin.settings.index');
    Route::post('/settings/profile', [SettingController::class, 'updateProfile'])->name('admin.settings.profile.update');
    Route::post('/settings/password', [SettingController::class, 'updatePassword'])->name('admin.settings.password.update');

    // Candidates
    Route::get('/candidates', [CandidateController::class, 'index'])->name('admin.candidates.index');
    Route::get('/candidates/create', [CandidateController::class, 'create'])->name('admin.candidates.create');
    Route::post('/candidates/store', [CandidateController::class, 'store'])->name('admin.candidates.store');

    Route::get('/candidates/{id}/edit', [CandidateController::class, 'edit'])->name('admin.candidates.edit');
    Route::post('/candidates/{id}/update', [CandidateController::class, 'update'])->name('admin.candidates.update');

    Route::get('/offer-letter/template', [OfferLetterController::class, 'editTemplate'])->name('admin.offerletter.template.edit');
    Route::get('/offer-letter/templates/{templateId}/edit', [OfferLetterController::class, 'editTemplate'])->name('admin.offerletter.template.edit.saved');
    Route::post('/offer-letter/template', [OfferLetterController::class, 'updateTemplate'])->name('admin.offerletter.template.update');
    Route::post('/offer-letter/template/images', [OfferLetterController::class, 'uploadImage'])->name('admin.offerletter.images.upload');
    Route::get('/offer-letter/template/images/{imageId}/delete', [OfferLetterController::class, 'deleteImage'])->name('admin.offerletter.images.delete');

    // Generate Offer Letter for Candidate
    Route::get('/offer-letter/generate/{candidateId}', [OfferLetterController::class, 'generate'])->name('admin.offerletter.generate');

    // Download Offer Letter PDF for Candidate
    Route::get('/offer-letter/download/{candidateId}', [OfferLetterController::class, 'download'])->name('admin.offerletter.download');




});
