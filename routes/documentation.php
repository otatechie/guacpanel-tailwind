<?php

use App\Http\Controllers\DocumentationController;

/*
|--------------------------------------------------------------------------
| Documentation Routes
|--------------------------------------------------------------------------
*/

Route::prefix('documentation')->name('documentation.')->group(function () {
    Route::controller(DocumentationController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/installation', 'installation')->name('installation');
        Route::get('/features', 'features')->name('features');
        Route::get('/components', 'components')->name('components');
    });
});
