<?php

use App\Http\Controllers\CaveController;
use App\Http\Controllers\homepageController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;

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
});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('varcave.profile');
    Route::get('/profile/theme/{theme}', [ProfileController::class, 'setTheme'])->name('varcave.profile.setTheme');
    Route::post('/some/where/over/the/rainbow', [ProfileController::class, 'someWhereOverTheRainbow'])->name('dummy.route'); //test route to avoid route name errors
    
});


//sensitive pages
Route::middleware(['auth', 'password.confirm'])->group(function () {
    Route::get('/profile/update-password', [ProfileController::class, 'showUpdatePassword'])->name('varcave.profile.show-password-update');
    Route::post('/profile/update-password', [ProfileController::class, 'updatePassword'])->name('varcave.profile.password-update');
});