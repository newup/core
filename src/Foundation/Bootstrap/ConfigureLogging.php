<?php

namespace NewUp\Foundation\Bootstrap;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Bootstrap\ConfigureLogging as LaravelConfigureLogging;
use Illuminate\Log\Writer;

class ConfigureLogging extends LaravelConfigureLogging
{

    /**
     * Configure the Monolog handlers for the application.
     *
     * @param  \Illuminate\Contracts\Foundation\Application $app
     * @param  \Illuminate\Log\Writer                       $log
     * @return void
     */
    protected function configureSingleHandler(Application $app, Writer $log)
    {
        $log->useFiles($app->storagePath() . '/logs/newup.log');
    }

    /**
     * Configure the Monolog handlers for the application.
     *
     * @param  \Illuminate\Contracts\Foundation\Application $app
     * @param  \Illuminate\Log\Writer                       $log
     * @return void
     */
    protected function configureDailyHandler(Application $app, Writer $log)
    {
        $log->useDailyFiles(
            $app->storagePath() . '/logs/newup.log',
            $app->make('config')->get('app.log_max_files', 5)
        );
    }

    /**
     * Configure the Monolog handlers for the application.
     *
     * @param  \Illuminate\Contracts\Foundation\Application $app
     * @param  \Illuminate\Log\Writer                       $log
     * @return void
     */
    protected function configureSyslogHandler(Application $app, Writer $log)
    {
        $log->useSyslog('newup');
    }

}