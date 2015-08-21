<?php

namespace NewUp\Console\Commands\Templates;

use Illuminate\Console\Command;
use NewUp\Contracts\Templates\StorageEngine;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class Install extends Command
{

    const INSTALL_FAIL = 0;
    const INSTALL_SUCCESS = 10;

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

        if ($this->option('confirm')) {
            if (!$this->confirm("Do you want to install the {$packageName} package template right now? [yes|no]",
                true)
            ) {
                $this->comment("Package installation aborted by user.");

                return self::INSTALL_FAIL;
            }
        }

        if ($this->templateStorageEngine->packageExists($packageName)) {
            if (!$this->confirm("The package {$packageName} is already installed. Would you like to update it instead? [yes|no]",
                false)
            ) {
                $this->comment("The package {$packageName} is already installed and will not be updated.");

                return self::INSTALL_SUCCESS;
            } else {
                $this->comment("Okay, we will update the {$packageName} package template. Give us a moment to get things ready...");
                $this->call('template:update', ['name' => $packageName]);

                return self::INSTALL_SUCCESS;
            }
        }

        $this->line("Installing {$packageName}. Sit back and relax for a minute, this can take a while.");
        $this->templateStorageEngine->addPackage($packageName);
        $this->info("{$packageName} installed.");
        return self::INSTALL_SUCCESS;
    }


    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the package template to install', null]
        ];
    }

    protected function getOptions()
    {
        return [
            ['confirm', null, InputOption::VALUE_NONE, 'If set, install requires confirmation']
        ];
    }

}