<?php

namespace NewUp\Providers;

use Illuminate\Support\ServiceProvider;
use NewUp\Contracts\Templates\PathNameParser;
use NewUp\Templates\Parsers\FileSystemPathNameParser;

class PathNameParserServiceProvider extends ServiceProvider
{

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(PathNameParser::class, function () {
            return $this->app->make(FileSystemPathNameParser::class);
        });
    }


}