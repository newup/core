<?php

return [

    'timezone'  => 'UTC',

    'debug'     => true,

    'log' => 'daily',

    'providers' => [
        /**
         * Relevant Laravel framework service providers.
         */
        'Illuminate\Bus\BusServiceProvider',
        'Illuminate\Encryption\EncryptionServiceProvider',
        'Illuminate\Filesystem\FilesystemServiceProvider',
        'Illuminate\Pipeline\PipelineServiceProvider',
        'Illuminate\Validation\ValidationServiceProvider',

        /**
         * NewUp specific service providers.
         */
        'NewUp\Providers\FilesystemServiceProvider',
        'NewUp\Providers\RendererServiceProvider',
        'NewUp\Providers\PathNameParserServiceProvider',
        'NewUp\Providers\GeneratorAnalyzerServiceProvider',
    ],

    'render_filters' => [
        'NewUp\Templates\Renderers\Filters\StudlyFilter',
        'NewUp\Templates\Renderers\Filters\CamelFilter',
        'NewUp\Templates\Renderers\Filters\LowerFilter',
        'NewUp\Templates\Renderers\Filters\PluralFilter',
        'NewUp\Templates\Renderers\Filters\SingularFilter',
        'NewUp\Templates\Renderers\Filters\SlugFilter',
        'NewUp\Templates\Renderers\Filters\SnakeFilter',
        'NewUp\Templates\Renderers\Filters\StudlyFilter',
        'NewUp\Templates\Renderers\Filters\UpperFilter',
    ],

    'render_functions' => [
        'NewUp\Templates\Renderers\Functions\PathNameFunction',
        'NewUp\Templates\Renderers\Functions\ArgumentFunction',
        'NewUp\Templates\Renderers\Functions\OptionFunction',
    ],

];