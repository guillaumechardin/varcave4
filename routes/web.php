<?php

use App\Http\Controllers\homepageController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;

/*
 **  Varcave public  routes **
 */
Route::group([], function (){
    //Route::view('/', 'varcave.home')->name('homepage');
    Route::get('myaccount/theme/{theme}', [ProfileController::class, 'setTheme'])->name('myaccount.setTheme');
    Route::get('/', [homepageController::class, 'displayHomepage'])->name('varcave.home');
});