<?php

namespace NewUp\Console\Commands;

use Illuminate\Console\Command;
use NewUp\Contracts\Templates\StorageEngine;
use Symfony\Component\Console\Input\InputArgument;

class Update extends Command
{

    protected $templateStorageEngine;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'template:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates a package template';

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


        $this->line("Updating {$packageName}. Sit back and relax for a minute, this can take a while (it usually takes a little longer than an install).");
        $this->templateStorageEngine->updatePackage($packageName);
        $this->info("{$packageName} updated.");
    }


    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the package template to update', null]
        ];
    }

    protected function getOptions()
    {
        return [];
    }

}