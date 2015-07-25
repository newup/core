<?php

namespace NewUp\Console;

class Kernel extends BaseKernel
{

    /**
     * Allows a command to override the InputInterface
     *
     * @var array
     */
    protected $commandInputOverrides = [
        'build' => '\NewUp\Console\Input\GeneratorInput',
        'a' => '\NewUp\Console\Input\GeneratorInput',
        'an' => '\NewUp\Console\Input\GeneratorInput'
    ];

    protected $commands = [
        'NewUp\Console\Commands\About',
        'NewUp\Console\Commands\Init',
        'NewUp\Console\Commands\Build',
        'NewUp\Console\Commands\Aliases\BuildAAlias',
        'NewUp\Console\Commands\Aliases\BuildAnAlias',
    ];

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
     * @param  \Symfony\Component\Console\Input\InputInterface  $input
     * @param  \Symfony\Component\Console\Output\OutputInterface  $output
     * @return int
     */
    public function handle($input, $output = null)
    {
        // The name of the command should be the first argument.
        $commandName = $input->getFirstArgument();

        if ($this->commandOverridesInputInterface($commandName))
        {
            $inputClass = $this->commandInputOverrides[$commandName];
            $input = new $inputClass;
        }

        try
        {
            $this->bootstrap();

            return $this->getArtisan()->run($input, $output);
        }
        catch (Exception $e)
        {
            $this->reportException($e);

            $this->renderException($output, $e);

            return 1;
        }
    }

}