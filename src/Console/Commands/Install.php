<?php

namespace NewUp\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use NewUp\Contracts\Templates\StorageEngine;

class Install extends Command
{

    protected $templateStorageEngine;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'template:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Installs a new package template';

    public function __construct(StorageEngine $storageEngine)
    {
        parent::__construct();
        $this->templateStorageEngine = $storageEngine;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $packageName = $this->argument('name');

        if ($this->templateStorageEngine->packageExists($packageName)) {
            $this->comment("The package {$packageName} is already installed.");
            return;
        }
        $this->line("Installing {$packageName}. Sit back and relax for a minute, this can take a while.");
        $this->templateStorageEngine->addPackage($packageName);
        $this->info("{$packageName} installed.");
    }


    protected function getArguments()
    {
        return [
          ['name', InputArgument::REQUIRED, 'The name of the package template to install', null]
        ];
    }

    protected function getOptions()
    {
        return [];
    }

}