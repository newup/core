<?php

namespace NewUp\Console\Commands\Templates;

use Illuminate\Console\Command;
use NewUp\Contracts\Templates\StorageEngine;
use Symfony\Component\Console\Input\InputArgument;

class Remove extends Command
{

    protected $templateStorageEngine;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'template:remove';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Removes a package template';

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
            $this->comment("The package {$packageName} is not currently installed.");

            return;
        }

        $this->line("Removing {$packageName}. Sit back and relax for a minute, this can take a while.");
        $this->templateStorageEngine->removePackage($packageName);
        $this->info("{$packageName} removed.");
    }


    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the package template to remove', null]
        ];
    }

    protected function getOptions()
    {
        return [];
    }

}