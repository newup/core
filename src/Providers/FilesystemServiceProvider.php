<?php

namespace NewUp\Providers;

use Illuminate\Support\ServiceProvider;

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
    }


}