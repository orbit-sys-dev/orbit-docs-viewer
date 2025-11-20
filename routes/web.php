<?php

use Illuminate\Support\Facades\Route;
use Orbit\DevDocsViewer\Http\Controllers\DocsViewerController;
use Orbit\DevDocsViewer\Http\Controllers\DocsViewerFileController;

Route::group([
    'prefix' => trim(config('docs_viewer.route.prefix'), '/'),
    'as' => config('docs_viewer.route.name_prefix'),
    'middleware' => config('docs_viewer.route.middleware', ['web']),
], function () {
    Route::get('/', [DocsViewerController::class, '__invoke'])->name('viewer');

    Route::get('/app', [DocsViewerController::class, 'app'])->name('app');

    Route::get('/development', [DocsViewerController::class, 'development'])->name('development');

    Route::get('/development/{group}', [DocsViewerController::class, 'developmentGroup'])
        ->where('group', '[A-Za-z0-9_-]+')
        ->name('development.group');

    Route::get('/file', DocsViewerFileController::class)->name('viewer.file');
});
