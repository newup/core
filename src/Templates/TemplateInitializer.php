<?php

namespace NewUp\Templates;

use NewUp\Configuration\ConfigurationWriter;
use NewUp\Contracts\Filesystem\Filesystem;
use NewUp\Contracts\Templates\Renderer;
use NewUp\Exceptions\InvalidPathException;

class TemplateInitializer
{

    /**
     * The Filesystem implementation.
     *
     * @var Filesystem
     */
    protected $files;

    /**
     * The Renderer implementation.
     *
     * @var Renderer
     */
    protected $renderer;

    public function __construct(Filesystem $files, Renderer $renderer)
    {
        $this->files    = $files;
        $this->renderer = $renderer;
    }

    /**
     * Initializes a package template in the provided directory.
     *
     * @throws InvalidPathException
     *
     * @param $vendor
     * @param $package
     * @param $directory
     */
    public function initialize($vendor, $package, $directory)
    {
        if (!$this->files->exists($directory) || !$this->files->isDirectory($directory)) {
            throw new InvalidPathException("{$directory} does not exist or is not a valid directory.");
        }

        $packageComposer = new Package;
        $packageComposer->setVendor($vendor);
        $packageComposer->setPackage($package);
        $packageComposer->setDescription('Give your package template a good description');
        $packageComposer->setLicense(config('user.configuration.license', ''));
        $packageComposer->setAuthors(config('user.configuration.authors', []));

        $writer = new ConfigurationWriter($packageComposer->toArray());

        $writer->save($directory.'/composer.json');
        $this->renderer->setData('package', $package);
        $this->renderer->setData('vendor', $vendor);

        $packageClass = $this->renderer->render('template');

        if (!$this->files->exists($directory.'/_newup/')) {
            $this->files->makeDirectory($directory.'/_newup/');
        }

        $this->files->put($directory.'/_newup/Package.php', $packageClass);
    }


}