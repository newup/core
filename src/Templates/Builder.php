<?php

namespace NewUp\Templates;

use NewUp\Console\Application;
use NewUp\Contracts\Filesystem\Filesystem;
use NewUp\Foundation\Composer\AutoLoaderManager;
use NewUp\Templates\Generators\ContentGenerator;
use NewUp\Templates\Generators\PathNormalizer;
use NewUp\Templates\Loaders\PackageLoader;
use NewUp\Templates\Renderers\Collectors\InputCollector;

class Builder
{

    use PathNormalizer;

    /**
     * The template directory.
     *
     * @var string|null
     */
    protected $templateDirectory = null;

    /**
     * The template name.
     *
     * @var string|null
     */
    protected $templateName = null;

    /**
     * The output directory.
     *
     * @var string
     */
    protected $outputDirectory = '';

    /**
     * The ContentGenerator instance.
     *
     * @var \NewUp\Templates\Generators\ContentGenerator
     */
    protected $generator;

    /**
     * The Filesystem implementation.
     *
     * @var \NewUp\Contracts\Filesystem\Filesystem
     */
    protected $files;

    /**
     * The PackageLoader instance.
     *
     * @var PackageLoader
     */
    protected $packageLoader;

    /**
     * The full-qualified name of the package template class.
     *
     * @var string
     */
    protected $packageClassName = '';

    /**
     * The package template instance.
     *
     * @var BasePackageTemplate
     */
    protected $package;

    /**
     * The InputCollector instance.
     *
     * @var \NewUp\Templates\Renderers\Collectors\InputCollector
     */
    protected $inputCollector;

    /**
     * The AutoLoaderManager instance.
     *
     * @var AutoLoaderManager
     */
    protected $autoLoaderManager;

    public function __construct(
        ContentGenerator $contentGenerator,
        Filesystem $files,
        PackageLoader $loader,
        InputCollector $inputCollector,
        AutoLoaderManager $manager
    ) {
        $this->generator         = $contentGenerator;
        $this->files             = $files;
        $this->packageLoader     = $loader;
        $this->inputCollector    = $inputCollector;
        $this->autoLoaderManager = $manager;
    }

    /**
     * Sets the template directory.
     *
     * @param $directory
     */
    public function setTemplateDirectory($directory)
    {
        // The directory will be null if a template is being loaded from storage.
        if ($directory == null) {
            $this->templateDirectory = realpath(find_tse_template($this->templateName));
            $this->loadPackageTemplate($this->templateDirectory);
            return;
        }

        $this->templateDirectory = realpath($directory);
        $this->loadPackageTemplate(realpath($directory));
    }

    /**
     * Sets the template name.
     *
     * @param $name
     */
    public function setTemplateName($name)
    {
        $this->templateName = $name;
    }

    /**
     * Sets the output directory.
     *
     * @param $directory
     */
    public function setOutputDirectory($directory)
    {
        $this->outputDirectory = $directory;
    }

    /**
     * This method will return the path that the engine should use
     * when retrieving template content. If the developer has
     * created a directory '_template' we will use that dir
     * instead.
     *
     * @return null|string
     */
    private function getTemplateDirectory()
    {
        $templateDirectory = realpath($this->templateDirectory . '/_template');

        if ($this->files->exists($templateDirectory) && $this->files->isDirectory($templateDirectory)) {
            $this->generator->setInsideTemplateDirectory(true);

            return $templateDirectory;
        }

        return $this->templateDirectory;
    }

    /**
     * Returns the common template directory.
     *
     * @return null|string
     */
    private function getCommonTemplateDirectory()
    {
        $commonDirectory = realpath($this->templateDirectory . '/_newup/common');

        if ($this->files->exists($commonDirectory) && $this->files->isDirectory($commonDirectory)) {
            return $commonDirectory;
        }

        return null;
    }

    /**
     * Builds the package template.
     */
    public function build()
    {
        $this->package->setRendererInstance($this->generator->getRenderer());
        $this->package->setApplication(app('NewUp\Console\Application'));
        $this->package->setOutputInstance(Application::$output);
        $this->package->setInputInstance(Application::$input);
        $this->package->builderLoaded();

        if ($this->getCommonTemplateDirectory() !== null) {
            $this->generator->getRenderer()->addPath($this->getCommonTemplateDirectory());
        }

        $this->generator->setVerbatimExcludePatterns($this->package->getVerbatimExcludePatterns());
        $this->generator->setVerbatimPatterns($this->package->getVerbatimPatterns());

        foreach ($this->package->getTransformPaths() as $pathKey => $processPath) {
            $this->generator->getPathManager()->getCollector()->addFileNames([$this->normalizePath($pathKey) => $this->normalizePath($processPath)]);
        }

        foreach ($this->package->getIgnoredPaths() as $ignoredPath) {
            $this->generator->getPathManager()->getGenerator()->addIgnoredPath($this->normalizePath($ignoredPath));
        }

        foreach ($this->package->getPathsToRemove() as $pathToRemove) {
            $this->generator->getPathManager()->getGenerator()->addAutomaticallyRemovedPath($this->normalizePath($pathToRemove));
        }

        $this->generator->addPaths((array)$this->getTemplateDirectory());
        $this->generator->generateContent($this->outputDirectory);
    }

    /**
     * This method will load the package template instance.
     *
     * @param $directory
     */
    private function loadPackageTemplate($directory)
    {
        $namespacedPackageClass = $this->packageLoader->loadPackage(realpath($directory));
        $this->autoLoaderManager->mergePackageLoader(realpath($directory));
        $this->package          = app($namespacedPackageClass);
    }

    /**
     * Sets the command options with their values.
     *
     * @param $options
     */
    public function setOptions($options)
    {
        $this->package->setParsedOptions($options);
        $this->inputCollector->setOptions($options);
    }

    /**
     * Sets the command arguments with their values.
     *
     * @param $arguments
     */
    public function setArguments($arguments)
    {
        $this->package->setParsedArguments($arguments);
        $this->inputCollector->setArguments($arguments);
    }

}