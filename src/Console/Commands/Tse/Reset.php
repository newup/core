<?php

namespace NewUp\Console\Commands\Tse;

use Illuminate\Console\Command;
use NewUp\Contracts\Templates\SearchableStorageEngine;

class Reset extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'tse:reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Resets the template storage to a clean state';

    /**
     * The SearchableStorageEngine implementation instance.
     *
     * @var SearchableStorageEngine
     */
    protected $templateStorageEngine;

    public function __construct(SearchableStorageEngine $templateStorageEngine)
    {
        parent::__construct();
        $this->templateStorageEngine = $templateStorageEngine;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if ( ! $this->confirm('This will completely remove all installed package templates. Continue?. [yes|no]', false))
        {
            $this->comment('Reset aborted by user');
            return;
        }

        $this->templateStorageEngine->reset();
        $this->info('Template storage reset');
    }

}