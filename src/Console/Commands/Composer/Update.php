<?php

namespace NewUp\Console\Commands\Composer;

use Illuminate\Console\Command;
use NewUp\Foundation\Composer\Composer;

class Update extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'composer:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks getcomposer.org for newer versions of composer and if found, installs the latest.';

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
        $wasUpdated = $this->composer->selfUpdate();

        if ($wasUpdated) {
            $this->info('Composer was updated');
            $this->comment($this->composer->getVersion());
        } else {
            $this->comment('Composer is already up to date');
        }

    }

}