<?php

namespace NewUp\Console\Commands;

use Illuminate\Console\Command;
use NewUp\Templates\Generators\PathNormalizer;

class About extends Command
{

    use PathNormalizer;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'about';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Display basic information about NewUp';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('NewUp version ' . $this->laravel->version());
        $this->line('http://newup.io' . PHP_EOL);
        $this->line('Configuration loaded from: '.$this->normalizePath(get_user_config_path()).PHP_EOL);
        $this->comment('NewUp is a simple command line utility, built on Laravel\'s Artisan, to quickly generate packages compatible with all of PHP.');
        $this->comment('Check out the source code at github.com/newup/newup' . PHP_EOL);
        $this->line('Thank you for using NewUp!');
    }

}