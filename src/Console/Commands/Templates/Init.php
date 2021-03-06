<?php

namespace NewUp\Console\Commands\Templates;

use Illuminate\Console\Command;
use NewUp\Contracts\Filesystem\Filesystem;
use NewUp\Exceptions\InvalidArgumentException;
use NewUp\Exceptions\InvalidPathException;
use NewUp\Templates\Package;
use NewUp\Templates\TemplateInitializer;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class Init extends Command
{

    protected $templateInitializer;

    protected $files;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'init';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initializes a new NewUp package template';

    public function __construct(TemplateInitializer $initializer, Filesystem $files)
    {
        parent::__construct();
        $this->setAliases(['template:init']);
        $this->templateInitializer = $initializer;
        $this->files = $files;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->line('Starting package template initialization...');
        try {
            $directory = $this->argument('directory');

            if (!$this->files->exists($directory)) {
                $createDirectory = $this->confirm("{$directory} does not exist. Would you like to create it? [YES/no]", true);

                if ($createDirectory) {
                    $this->line("{$directory} was created.");
                    $this->files->makeDirectory($directory);
                }
            }

            if ($this->files->exists($directory) && $this->files->isDirectory($directory)) {
                $fileCount = count($this->files->allFiles($directory));

                if ($fileCount > 0) {
                    $removeFiles = $this->confirm("{$directory} is not empty. Would you like to remove the contents? [yes|NO]", false);

                    if ($removeFiles) {
                        $this->line("The contents in '{$directory}' were cleared.");
                        $this->files->deleteDirectory($directory, true);
                    }
                }
            }

            $this->templateInitializer->setShouldCreateTemplateDirectory(!$this->option('no-template-dir'));

            $packageVendor = Package::parseVendorAndPackage($this->argument('name'));
            $this->templateInitializer->initialize($packageVendor[0], $packageVendor[1], $directory);
            $this->info('Package was successfully initialized!');
        } catch (InvalidPathException $invalidPath) {
            $this->error('There was a problem initializing the package template (most likely due to an invalid path). The error was:');
            $this->error($invalidPath->getMessage());
        } catch (InvalidArgumentException $invalidVendorPackage) {
            $this->error('There was a problem initializing the package template');
            $this->error($invalidVendorPackage->getMessage());
        }
    }

    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The vendor/package name of the new package template', null],
            ['directory', InputArgument::REQUIRED, 'The directory to initialize the package template', null]
        ];
    }

    protected function getOptions()
    {
        return [
          ['no-template-dir', 't', InputOption::VALUE_NONE, 'If set, a "_template" directory will be not created', null]
        ];
    }


}