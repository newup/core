<?php

namespace NewUp\Console\Commands;

use Illuminate\Console\Command;
use NewUp\Contracts\Templates\StorageEngine;
use Symfony\Component\Console\Input\InputArgument;

class Reconfigure extends Command
{

    protected $templateStorageEngine;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'template:reconfigure';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reconfigures a package template by updating its dependencies';

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

        if (!$this->templateStorageEngine->packageExists($packageName)) {
            if (!$this->confirm("The package {$packageName} is not currently installed. Would you like to install it instead? [yes|no]",
                false)
            ) {
                $this->comment("The package {$packageName} is not currently installed, and will not be installed right now.");

                return;
            } else {
                $this->comment("Okay, we will install the {$packageName} package template. Give us a moment to get things ready...");
                $this->call('template:install', ['name' => $packageName]);

                return;
            }
        }


        $this->line("Updating {$packageName} dependencies. Sit back and relax for a minute, this can take a while.");
        $this->templateStorageEngine->configurePackage($packageName);
        $this->info("{$packageName} dependencies updated.");
    }


    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the package template to reconfigure', null]
        ];
    }

    protected function getOptions()
    {
        return [];
    }

}