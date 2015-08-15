<?php

namespace NewUp\Providers;

use Illuminate\Support\ServiceProvider;
use NewUp\Filesystem\TemplateStorageEngine;
use NewUp\Foundation\Composer\AutoLoaderManager;

class FilesystemServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('NewUp\Contracts\Filesystem\Filesystem', 'NewUp\Filesystem\Filesystem');

        $this->app->singleton('NewUp\Contracts\Templates\StorageEngine', function () {
            return new TemplateStorageEngine(app('NewUp\Contracts\Filesystem\Filesystem'),
                app('NewUp\Foundation\Composer\Composer'),
                template_storage_path(),
                app('Illuminate\Contracts\Logging\Log')
            );
        });

        $this->app->singleton('NewUp\Foundation\Composer\AutoLoaderManager', function() {
            return new AutoLoaderManager(app('NewUp\Contracts\Filesystem\Filesystem'), app(), app('Illuminate\Contracts\Logging\Log'));
        });
    }


}