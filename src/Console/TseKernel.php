<?php

namespace NewUp\Console;

class TseKernel extends Kernel {

    protected $commands = [
        'NewUp\Console\Commands\Tse\Analyze',
    ];

}