<?php

use App\Http\Controllers\AboutPageController;
use App\Http\Controllers\CaveController;
use App\Http\Controllers\EulaController;
use App\Http\Controllers\FileResourcesController;
use App\Http\Controllers\HomepageController;
use App\Http\Controllers\PageFieldsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
 **  Varcave public  routes **
 */
Route::group([], function (){
    Route::get('/', [HomepageController::class, 'show'])->name('varcave.homepage');
    
    //CAVES PUBLIC PAGES
    //*** search page***
    Route::get('/caves/quicksearch', [CaveController::class, 'quicksearch'])->name('varcave.caves.quicksearch');
    Route::get('/caves/search', [CaveController::class, 'showSearchPage'])->name('varcave.caves.search');
    Route::post('/caves/search', [CaveController::class, 'stdSearch'])->middleware('throttle:9,1')->name('varcave.caves.stdSearch');
    Route::post('/caves/search-by-coords', [CaveController::class, 'searchByCoords'])->name('varcave.caves.searchByCoords');
    
    
    //*** Cave info display ***
    Route::get('/caves/{uuid}', [CaveController::class, 'show'])->whereUuid('uuid')->name('varcave.caves.show');
    Route::get('/caves', [CaveController::class, 'showSearchPage'])->name('varcave.caves.all');
    Route::post('/caves/{uuid}/update-request', [CaveController::class, 'emailUpdateRequest'])
    ->whereUuid('uuid')
    ->middleware('throttle:3,5')
    ->name('varcave.caves.emailUpdateRequest'); 

    //no more used
    //Route::get('/vm', [CaveController::class, 'vm'])->name('varcave.vm');
    
    //PUBLIC RESOURCES PAGES
    Route::get('/resources/{fileResource}', [FileResourcesController::class, 'get'])->name('varcave.resource.download');
    Route::get('/resources', [FileResourcesController::class, 'show'])->name('varcave.resource.show');

    //SET THEME ROUTE
    Route::post('/guest/theme', [ProfileController::class, 'storeTheme'])->name('varcave.guest.theme.store');

    Route::get('/statistics', [CaveController::class, 'viewStats'])->name('varcave.caves.statistics');

    Route::get('/about', [AboutPageController::class, 'show'])->name('varcave.about.show');
    
});


Route::middleware('auth')->group(function () {
    //SEARCH by COORDINATES
    Route::get('/caves/spatial-search', [CaveController::class, 'spatialSearchShow'])->name('varcave.caves.spatialSearchShow');
    Route::post('/caves/spatial-search', [CaveController::class, 'spatialSearch'])->middleware('throttle:30,1')->name('varcave.caves.spatialSearch');

    //CAVES
    Route::post('/caves/', [CaveController::class, 'create'])->name('varcave.caves.create');
    Route::get('/caves/{uuid}/map', [CaveController::class, 'getMap'])->whereUuid('uuid')->middleware('throttle:20,1')->name('varcave.caves.map');
    Route::get('/caves/{uuid}/gpx', [CaveController::class, 'getGpx'])->whereUuid('uuid')->middleware('throttle:20,1')->name('varcave.caves.gpx');
    Route::get('/caves/{uuid}/pdf', [CaveController::class, 'getPdf'])->whereUuid('uuid')->middleware('throttle:20,1')->name('varcave.caves.pdf');
    Route::get('/caves/{uuid}/edit', [CaveController::class, 'caveEditPage'])->whereUuid('uuid')->name('varcave.caves.caveEditPage');
    Route::post('/caves/{uuid}', [CaveController::class, 'updateCaveData'])->whereUuid('uuid')->name('varcave.caves.updateCaveData');
    Route::post('/caves/{uuid}/copy', [CaveController::class, 'copy'])->whereUuid('uuid')->name('varcave.caves.copy');
    Route::post('/caves/{uuid}/coord', [CaveController::class, 'addCoord'])->whereUuid('uuid')->name('varcave.caves.coord.store');
    Route::put('/caves/{uuid}/coord', [CaveController::class, 'updateCoord'])->whereUuid('uuid')->name('varcave.caves.coord.update');
    Route::delete('/caves/{uuid}/coord', [CaveController::class, 'destroyCoord'])->whereUuid('uuid')->name('varcave.caves.coord.destroy');
    Route::post('/caves/{uuid}/file', [CaveController::class, 'createFile'])->whereUuid('uuid')->name('varcave.caves.file.create');
    Route::delete('/caves/{uuid}/file', [CaveController::class, 'destroyFile'])->whereUuid('uuid')->name('varcave.caves.file.destroy');
    Route::patch('/caves/{uuid}/file', [CaveController::class, 'patchFile'])->whereUuid('uuid')->name('varcave.caves.file.patch');
    Route::get('/cave/{uuid}/staticmap', [CaveController::class, 'getStaticMap'])->whereUuid('uuid')->name('varcave.staticmap');
    Route::post('/caves/{uuid}/changelog', [CaveController::class, 'createChangelog'])->whereUuid('uuid')->name('varcave.caves.createChangelog');
    Route::patch('/caves/{uuid}/changelog', [CaveController::class, 'updateChangelog'])->whereUuid('uuid')->name('varcave.caves.updateChangelog');
    Route::delete('/caves/{uuid}/changelog', [CaveController::class, 'destroyChangelog'])->whereUuid('uuid')->name('varcave.caves.destroyChangelog');
    Route::post('/caves/{uuid}/bibliography', [CaveController::class, 'createBibliography'])->whereUuid('uuid')->name('varcave.caves.createBibliography');
    Route::patch('/caves/{uuid}/bibliography', [CaveController::class, 'updateBibliography'])->whereUuid('uuid')->name('varcave.caves.updateBibliography');
    Route::delete('/caves/{uuid}/bibliography', [CaveController::class, 'removeBibliography'])->whereUuid('uuid')->name('varcave.caves.removeBibliography');
    
    //PROFILE
    
    Route::get('/profile', [ProfileController::class, 'show'])->name('varcave.profile');
    route::get('/profile/eula', [ProfileController::class, 'showEULA'])->name('varcave.profile.eula.show');
    Route::patch('/profile/eula', [ProfileController::class, 'updateEULA'])->name('varcave.profile.eula.update');
    Route::patch('/profile/preference', [ProfileController::class, 'updatePreference'])->name('varcave.profile.updatePreference');
    Route::delete('/profile/bookmark/{bookmark}', [ProfileController::class, 'deleteBookmark'])->name('varcave.profile.bookmark.delete');
    Route::post('/profile/theme', [ProfileController::class, 'storeTheme'])->name('varcave.profile.theme.store');
    Route::post('/profile/bookmark', [ProfileController::class, 'storeBookmark'])->name('varcave.profile.bookmark.store');
    
    //RESOURCES
    Route::post('/resource', [FileResourcesController::class, 'store'])->name('varcave.resource.store');
    Route::patch('/resource/{fileResource}', [FileResourcesController::class, 'update'])->name('varcave.resource.update');
    Route::delete('/resource/{fileResource}', [FileResourcesController::class, 'destroy'])->name('varcave.resource.delete');
    Route::post('/resource/buildgpx', [FileResourcesController::class, 'buildGpxFullData'])->name('varcave.resources.buildgpxdata');
});

Route::middleware(['auth', 'can:admin-access'])->group(function () {
    //SETTINGS
    Route::get('/admin/settings', [SettingController::class, 'show'])->name('varcave.admin-settings');
    Route::patch('/admin/settings/{setting}', [SettingController::class, 'updateValue'])->name('varcave.admin-settings-update');
    Route::patch('/admin/settings/overridable/{setting}', [SettingController::class, 'updateOverridable'])->name('varcave.admin-settings.update-overridable');

    Route::get('/admin/supportinfo', [SettingController::class, 'supportinfo'])->name('varcave.support-info');
    
    //USER MGMT
    Route::get('/admin/users', [UserController::class, 'index'])->name('varcave.users.index');
    Route::get('/admin/users/{user}', [UserController::class, 'getUserModalForm'])->name('varcave.users.user-modal-form');
    Route::put('/admin/users/{user}', [UserController::class, 'save'])->name('varcave.users.save');
    Route::delete('/admin/users/{user}', [UserController::class, 'destroy'])->name('varcave.users.delete');

    //USERS MASS IMPORT
    Route::post('/admin/users/import', [UserController::class, 'import'])->name('varcave.users.import');
    
    //USER ROLES MGMT
    Route::get('/admin/users/roles/{user}', [UserController::class, 'getRoleModalForm'])->name('varcave.users.role');
    Route::put('/admin/users/roles/{user}', [UserController::class, 'roleSave'])->name('varcave.users.role-save');

    //EULA
    Route::get('/admin/eula', [EulaController::class, 'show'])->name('varcave.eula.show');
    Route::patch('/admin/eula/{eula}', [EulaController::class, 'update'])->name('varcave.eula.update');

    //PAGE FIELDS
    Route::get('/admin/pagefields', [PageFieldsController::class, 'show'])->name('varcave.pagefield.show');
    Route::patch('/admin/pagefields', [PageFieldsController::class, 'reorder'])->name('varcave.pagefield.reorder');
    Route::patch('/admin/pagefields/{pagefield}', [PageFieldsController::class, 'update'])->name('varcave.pagefield.update');
    
   
});


//sensitive pages need pwd confirmation
Route::middleware(['auth', 'password.confirm'])->group(function () {
    Route::get('/profile/update-password', [ProfileController::class, 'showUpdatePassword'])->name('varcave.profile.show-password-update');
    Route::post('/profile/update-password', [ProfileController::class, 'updatePassword'])->name('varcave.profile.password-update');
});