<?php

namespace NewUp\Console\Commands\Composer;

use Illuminate\Console\Command;
use NewUp\Foundation\Composer\Composer;

class Version extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'composer:version';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Displays the Composer version that the system is using';

    /**
     * The Composer instance.
     *
     * @var Composer
     */
    protected $composer;

    public function __construct(Composer $composer)
    {
        parent::__construct();
        $this->composer = $composer;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->line($this->composer->getVersion());
    }

}