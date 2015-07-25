<?php

namespace NewUp\Providers;

use Illuminate\Support\ServiceProvider;

class PathNameParserServiceProvider extends ServiceProvider
{

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('NewUp\Contracts\Templates\PathNameParser', function () {
            return $this->app->make('NewUp\Templates\Parsers\FileSystemPathNameParser');
        });
    }


}