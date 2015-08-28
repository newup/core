<?php

return [

    'timezone'  => 'UTC',

    'debug'     => true,

    'log' => 'daily',

    'providers' => [
        /**
         * Relevant Laravel framework service providers.
         */
        \Illuminate\Bus\BusServiceProvider::class,
        \Illuminate\Encryption\EncryptionServiceProvider::class,
        \Illuminate\Filesystem\FilesystemServiceProvider::class,
        \Illuminate\Pipeline\PipelineServiceProvider::class,
        \Illuminate\Validation\ValidationServiceProvider::class,

        /**
         * NewUp specific service providers.
         */
        \NewUp\Providers\FilesystemServiceProvider::class,
        \NewUp\Providers\RendererServiceProvider::class,
        \NewUp\Providers\PathNameParserServiceProvider::class,
        \NewUp\Providers\GeneratorAnalyzerServiceProvider::class,
    ],

    'render_filters' => [
        \NewUp\Templates\Renderers\Filters\StudlyFilter::class,
        \NewUp\Templates\Renderers\Filters\CamelFilter::class,
        \NewUp\Templates\Renderers\Filters\LowerFilter::class,
        \NewUp\Templates\Renderers\Filters\PluralFilter::class,
        \NewUp\Templates\Renderers\Filters\SingularFilter::class,
        \NewUp\Templates\Renderers\Filters\SlugFilter::class,
        \NewUp\Templates\Renderers\Filters\SnakeFilter::class,
        \NewUp\Templates\Renderers\Filters\StudlyFilter::class,
        \NewUp\Templates\Renderers\Filters\UpperFilter::class,
    ],

    'render_functions' => [
        \NewUp\Templates\Renderers\Functions\PathNameFunction::class,
        \NewUp\Templates\Renderers\Functions\ArgumentFunction::class,
        \NewUp\Templates\Renderers\Functions\OptionFunction::class,
    ],

];