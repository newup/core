<?php

namespace NewUp\Console\Commands\Templates;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use NewUp\Contracts\Templates\SearchableStorageEngine;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class Search extends Command
{

    protected $templateStorageEngine;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'template:search';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Searches installed templates and displays the results';

    public function __construct(SearchableStorageEngine $storageEngine)
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
        if ($this->option('vendor')) {
            $this->displayPackageVendors();
            return;
        }

        $this->displayPackages();
    }

    private function displayPackageVendors()
    {
        $vendors = $this->templateStorageEngine->getInstalledVendors();
        $displayVendors = [];

        if ($this->argument('q') !== null) {
            foreach ($vendors as $vendor) {
                if (Str::is($this->argument('q'), $vendor['vendor'])) {
                    $displayVendors[] = $vendor;
                }
            }
        } else {
            $displayVendors = $vendors;
        }

        $this->table(['Vendor', 'Installation Directory'], $displayVendors);
    }

    private function displayPackages()
    {
        $vendors = $this->templateStorageEngine->getInstalledPackages();
        $displayPackages = [];


        foreach ($vendors as $vendor) {
            foreach ($vendor['packages'] as $package) {

                if ($this->argument('q') !== null) {
                    if (Str::is($this->argument('q'), $package['package']) === false) {
                        continue;
                    }
                }

                $displayPackages[] = [
                  $package['package'], $vendor['vendor'], $package['version'], $package['instance']->getDescription()
                ];
            }
        }

        $this->table(['Package', 'Vendor', 'Version', 'Description'], $displayPackages);
    }

    protected function getArguments()
    {
        return [
            ['q', InputArgument::OPTIONAL, 'Optional search query', null]
        ];
    }

    protected function getOptions()
    {
        return [
            ['vendor', 'vd', InputOption::VALUE_NONE, 'If set, only vendors will be returned'],
            ['packages', 'p', InputOption::VALUE_NONE, 'If set, only packages will be returned (default behavior)'],
        ];
    }

}