<?php

namespace NewUp\Providers;

use Illuminate\Support\ServiceProvider;
use NewUp\Filesystem\TemplateStorageEngine;

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
        $this->app->singleton('NewUp\Contracts\Templates\StorageEngine', function() {
           return new TemplateStorageEngine(app('NewUp\Contracts\Filesystem\Filesystem'), template_storage_path());
        });
    }


}