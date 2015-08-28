<?php

namespace NewUp\Console;

use NewUp\Console\Input\GeneratorInput;
use NewUp\Console\Commands\About;
use NewUp\Console\Commands\Templates\Init;
use NewUp\Console\Commands\Templates\Build;
use NewUp\Console\Commands\Templates\Install;
use NewUp\Console\Commands\Templates\Remove;
use NewUp\Console\Commands\Templates\Update;
use NewUp\Console\Commands\Templates\Reconfigure;
use NewUp\Console\Commands\Templates\Search;
use NewUp\Console\Commands\Composer\Which as ComposerWhich;
use NewUp\Console\Commands\Composer\Version as ComposerVersion;
use NewUp\Console\Commands\Composer\Update as ComposerUpdate;
use NewUp\Console\Commands\Tse\Analyze as TseAnalyze;
use NewUp\Console\Commands\Tse\Reset as TseReset;

class Kernel extends BaseKernel
{

    /**
     * Allows a command to override the InputInterface
     *
     * @var array
     */
    protected $commandInputOverrides = [
        'build'          => GeneratorInput::class,
        'a'              => GeneratorInput::class,
        'an'             => GeneratorInput::class,
        'template:build' => GeneratorInput::class,
    ];

    protected $commands = [
        About::class,
        Init::class,
        Build::class,
        Install::class,
        Remove::class,
        Update::class,
        Reconfigure::class,
        Search::class,
        ComposerWhich::class,
        ComposerVersion::class,
        ComposerUpdate::class,
    ];

    protected function getCommands()
    {
        if (user_config('configuration.enableUtilityCommands', false)) {
            // Enable the TSE Utility Commands
            return array_merge($this->commands, [
                TseAnalyze::class,
                TseReset::class,
            ]);
        }

        return $this->commands;
    }


    /**
     * Determines if a command overrides the input interface.
     *
     * @param $command
     * @return bool
     */
    private function commandOverridesInputInterface($command)
    {
        return array_key_exists($command, $this->commandInputOverrides);
    }

    /**
     * Run the console application.
     *
     * @param  \Symfony\Component\Console\Input\InputInterface   $input
     * @param  \Symfony\Component\Console\Output\OutputInterface $output
     * @return int
     */
    public function handle($input, $output = null)
    {
        // The name of the command should be the first argument.
        $commandName = $input->getFirstArgument();

        if ($this->commandOverridesInputInterface($commandName)) {
            $inputClass = $this->commandInputOverrides[$commandName];
            $input      = new $inputClass;
        }

        try {
            $this->bootstrap();

            return $this->getArtisan()->run($input, $output);
        } catch (Exception $e) {
            $this->reportException($e);

            $this->renderException($output, $e);

            return 1;
        }
    }

    public function getApplication()
    {
        return $this->getArtisan();
    }

}