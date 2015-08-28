<?php

namespace NewUp\Providers;

use Illuminate\Support\ServiceProvider;
use NewUp\Filesystem\TemplateStorageEngine;
use NewUp\Foundation\Composer\AutoLoaderManager;
use NewUp\Contracts\Filesystem\Filesystem as FilesystemContract;
use NewUp\Filesystem\Filesystem;
use NewUp\Contracts\Templates\StorageEngine;
use NewUp\Foundation\Composer\Composer;
use Illuminate\Contracts\Logging\Log;
use NewUp\Contracts\Templates\SearchableStorageEngine;

class FilesystemServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(FilesystemContract::class, Filesystem::class);

        $this->app->singleton(StorageEngine::class, function () {
            return new TemplateStorageEngine(app(FilesystemContract::class),
                app(Composer::class),
                template_storage_path(),
                app(Log::class)
            );
        });

        $this->app->singleton(SearchableStorageEngine::class, StorageEngine::class);

        $this->app->singleton(AutoLoaderManager::class, function() {
            return new AutoLoaderManager(app(FilesystemContract::class), app(), app(Log::class));
        });
    }


}