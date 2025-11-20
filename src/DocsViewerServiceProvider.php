<?php

namespace Orbit\DevDocsViewer;

use Illuminate\Support\ServiceProvider;

class DocsViewerServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/docs_viewer.php',
            'docs_viewer'
        );

        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'docs-viewer');
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'docs-viewer');
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');

        $this->publishes([
            __DIR__ . '/../config/docs_viewer.php' => config_path('docs_viewer.php'),
        ], 'docs-viewer-config');

        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/docs-viewer'),
        ], 'docs-viewer-views');

        $this->publishes([
            __DIR__ . '/../resources/lang' => resource_path('lang/vendor/docs-viewer'),
        ], 'docs-viewer-lang');
    }
}
