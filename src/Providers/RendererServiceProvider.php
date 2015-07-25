<?php

namespace NewUp\Providers;

use Illuminate\Support\ServiceProvider;

class RendererServiceProvider extends ServiceProvider
{

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('NewUp\Contracts\Templates\Renderer', function () {
            return $this->app->make('NewUp\Templates\Renderers\TemplateRenderer');
        });
    }


}