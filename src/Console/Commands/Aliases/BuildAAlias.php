<?php

namespace NewUp\Console\Commands\Aliases;

use NewUp\Console\Commands\Build;

class BuildAAlias extends Build
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'a';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Builds a package from a specified template (alias of build)';

}