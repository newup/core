<?php

namespace NewUp\Providers;

use Illuminate\Support\ServiceProvider;
use NewUp\Templates\Renderers\Collectors\InputCollector;
use NewUp\Contracts\Templates\Renderer;
use NewUp\Templates\Renderers\TemplateRenderer;

class RendererServiceProvider extends ServiceProvider
{

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(InputCollector::class, function() {
            return new InputCollector;
        });

        $this->app->singleton(Renderer::class, function () {
            return $this->app->make(TemplateRenderer::class);
        });
    }


}