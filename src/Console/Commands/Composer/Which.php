<?php

namespace NewUp\Console\Commands\Composer;

use Illuminate\Console\Command;
use NewUp\Foundation\Composer\Composer;

class Which extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'composer:which';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Displays which Composer.phar the system is using';

    /**
     * The Composer instance.
     *
     * @var Composer
     */
    protected $composer;

    public function __construct(Composer $composer)
    {
        parent::__construct();
        $this->composer = $composer;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->line('NewUp is using the Composer that can be found at this location:');

        $composer = $this->composer->findComposer();

        $this->line($composer);

        $formatter = $this->getHelper('formatter');
        if ($composer == 'composer') {
            $errorMessages = [
                'It appears that your composer.phar file is aliased, or set in a PATH variable',
                'To find out where it is at, run the relevant command for your system:',
                'Windows: where '.$composer,
                'Linux:   which '.$composer
            ];

            $formattedBlock = $formatter->formatBlock($errorMessages, 'comment', true);
            $this->output->writeln($formattedBlock);
        }
    }

}