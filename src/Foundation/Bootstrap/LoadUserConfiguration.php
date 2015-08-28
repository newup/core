<?php

namespace NewUp\Foundation\Bootstrap;

use Illuminate\Config\Repository;
use NewUp\Templates\Generators\PathNormalizer;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Bootstrap\LoadConfiguration;

class LoadUserConfiguration extends LoadConfiguration
{

    use PathNormalizer;

    protected $userConfigurationPath = '';

    public function bootstrap(Application $app)
    {
        $app->instance('config.user', $config = new Repository);
        $this->loadConfigurationFiles($app, $config);
    }

    protected function getConfigurationFiles(Application $app)
    {
        $files = [];

        $userConfigurationPath = get_user_config_path();

        $userConfigurationPath = $this->normalizePath($userConfigurationPath);
        $this->userConfigurationPath = $userConfigurationPath;


        foreach (Finder::create()->files()->name('*.php')->in($userConfigurationPath) as $file) {
            $nesting = $this->getConfigurationNesting($file);

            $files[$nesting.basename($file->getRealPath(), '.php')] = $file->getRealPath();
        }

        return $files;
    }

    /**
     * Get the configuration file nesting path.
     *
     * @param  \Symfony\Component\Finder\SplFileInfo  $file
     * @return string
     */
    private function getConfigurationNesting(SplFileInfo $file)
    {
        $directory = $this->normalizePath(dirname($file->getRealPath()));
        $directory = ltrim($directory, $this->userConfigurationPath);

        if ($tree = trim(str_replace(config_path(), '', $directory), DIRECTORY_SEPARATOR))
        {
            $tree = str_replace(DIRECTORY_SEPARATOR, '.', $tree).'.';
        }

        return $tree;
    }

}