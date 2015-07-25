<?php

namespace NewUp\Providers;

use Illuminate\Support\ServiceProvider;
use NewUp\Templates\Renderers\Collectors\InputCollector;

class RendererServiceProvider extends ServiceProvider
{

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('NewUp\Templates\Renderers\Collectors\InputCollector', function() {
            return new InputCollector;
        });

        $this->app->singleton('NewUp\Contracts\Templates\Renderer', function () {
            return $this->app->make('NewUp\Templates\Renderers\TemplateRenderer');
        });
    }


}