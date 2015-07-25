<?php

namespace NewUp\Console\Commands;

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
        try {
            $directory = $this->argument('directory');

            if (!$this->files->exists($directory)) {
                $createDirectory = $this->confirm("{$directory} does not exist. Would you like to create it? [yes|no]", true);

                if ($createDirectory) {
                    $this->files->makeDirectory($directory);
                }
            }

            if ($this->files->exists($directory) && $this->files->isDirectory($directory)) {
                $fileCount = count($this->files->allFiles($directory));

                if ($fileCount > 0) {
                    $removeFiles = $this->confirm("{$directory} is not empty. Would you like to remove the contents? [yes|no]", false);

                    if ($removeFiles) {
                        $this->files->deleteDirectory($directory, true);
                    }
                }
            }

            $this->templateInitializer->setShouldCreateTemplateDirectory($this->option('template-dir'));

            $packageVendor = Package::parseVendorAndPackage($this->argument('name'));
            $this->templateInitializer->initialize($packageVendor[0], $packageVendor[1], $directory);
        } catch (InvalidPathException $invalidPath) {
            $this->error($invalidPath->getMessage());
        } catch (InvalidArgumentException $invalidVendorPackage) {
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
          ['template-dir', 't', InputOption::VALUE_NONE, 'If set, a "_template" directory will be created', null]
        ];
    }


}