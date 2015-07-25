<?php

namespace NewUp\Console\Commands;

use Illuminate\Console\Command;
use NewUp\Templates\Builder;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class Build extends Command
{

    /**
     * The Builder instance.
     *
     * @var Builder
     */
    protected $templateBuilder;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'build';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Builds a package from a specified template';

    public function __construct(Builder $templateBuilder)
    {
        parent::__construct();
        $this->setAliases(['a', 'an']);
        $this->templateBuilder = $templateBuilder;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $this->templateBuilder->setTemplateName($this->argument('template'));
            $this->templateBuilder->setTemplateDirectory($this->option('newup-directory'));
            $this->templateBuilder->setOutputDirectory($this->argument('output-directory'));

            // Set the arguments and options for the package builder, etc.
            $this->templateBuilder->setOptions($this->input->getOptions());
            $this->templateBuilder->setArguments($this->input->getArguments());

            $this->templateBuilder->build();
        } catch (\Exception $e) {
            $this->error($e->getTraceAsString());
            $this->error($e->getMessage(), $e->getLine(), $e->getFile());
        }
    }

    protected function getArguments()
    {
        return [
            ['template', InputArgument::REQUIRED, 'The template name', null],
            ['output-directory', InputArgument::REQUIRED, 'The directory the built template should be saved to', null]
        ];
    }

    protected function getOptions()
    {
        return [
            ['newup-directory', null, InputOption::VALUE_OPTIONAL, 'Builds a package template from a local directory', null]
        ];
    }


}