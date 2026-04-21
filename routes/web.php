<?php

use App\Http\Controllers\CaveController;
use App\Http\Controllers\FileResourcesController;
use App\Http\Controllers\HomepageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
 **  Varcave public  routes **
 */
Route::group([], function (){
    
    Route::get('/', [HomepageController::class, 'displayHomepage'])->name('varcave.homepage');
    Route::get('/caves/search', [CaveController::class, 'search'])->name('varcave.caves.search');
    Route::get('/caves/{uuid}', [CaveController::class, 'show'])->whereUuid('uuid')->name('varcave.caves.show');
    Route::get('/caves', [CaveController::class, 'search'])->name('varcave.caves.all');
    Route::get('/vm', [CaveController::class, 'vm'])->name('varcave.vm');
    Route::get('/caves/quicksearch', [CaveController::class, 'quicksearch'])->name('varcave.caves.quicksearch');
    Route::get('/statistics', [CaveController::class, 'viewStats'])->name('varcave.caves.statistics');
    
    Route::get('/resources', [FileResourcesController::class, 'show'])->name('varcave.resources.file-show');
    
    Route::get('/test', [CaveController::class, 'test'])->name('varcave.caves.test');
});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('varcave.profile');
    Route::get('/caves/{uuid}/gpx', [CaveController::class, 'getGpx'])->whereUuid('uuid')->name('varcave.caves.gpx');
    Route::get('/caves/{uuid}/pdf', [CaveController::class, 'getPdf'])->whereUuid('uuid')->name('varcave.caves.pdf');

    //Route::get('/users/{user}', [UserController::class, 'show'])->name('varcave.users.show');
    
    Route::post('/profile/theme', [ProfileController::class, 'storeTheme'])->name('varcave.profile.theme.store');
    Route::post('/profile/bookmark', [ProfileController::class, 'storeBookmark'])->name('varcave.profile.bookmark.store');
    
    
    Route::delete('/profile/bookmark/{bookmark}', [ProfileController::class, 'deleteBookmark'])->name('varcave.profile.bookmark.delete');
    
});

Route::middleware(['auth', 'can:admin-access'])->group(function () {
    Route::get('/admin/settings', [SettingController::class, 'show'])->name('varcave.admin-settings');
    Route::get('/admin/supportinfo', [SettingController::class, 'supportinfo'])->name('varcave.support-info');
    Route::get('/admin/users', [UserController::class, 'index'])->name('varcave.users.index');
    Route::get('/admin/users/{user}', [UserController::class, 'getUserModalForm'])->name('varcave.users.user-modal-form');
    Route::get('/admin/users/roles/{user}', [UserController::class, 'getRoleModalForm'])->name('varcave.users.role');

    Route::post('/admin/users/import', [UserController::class, 'import'])->name('varcave.users.import');
    
    Route::patch('/admin/settings/{setting}', [SettingController::class, 'update'])->name('varcave.admin-settings-update');
    
    Route::put('/admin/users/{user}', [UserController::class, 'save'])->name('varcave.users.save');
    Route::put('/admin/users/roles/{user}', [UserController::class, 'roleSave'])->name('varcave.users.role-save');

    Route::delete('/admin/users/{user}', [UserController::class, 'destroy'])->name('varcave.users.delete');
});


//sensitive pages
Route::middleware(['auth', 'password.confirm'])->group(function () {
    Route::get('/profile/update-password', [ProfileController::class, 'showUpdatePassword'])->name('varcave.profile.show-password-update');
    Route::post('/profile/update-password', [ProfileController::class, 'updatePassword'])->name('varcave.profile.password-update');
});