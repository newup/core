<?php

namespace NewUp\Console\Commands\Aliases;

use NewUp\Console\Commands\Build;

class BuildAnAlias extends Build
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'an';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Builds a package from a specified template (alias of build)';

}