<?php

namespace NewUp\Templates;

use NewUp\Contracts\Filesystem\Filesystem;
use NewUp\Templates\Generators\ContentGenerator;

class Builder
{

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

    public function __construct(ContentGenerator $contentGenerator, Filesystem $files)
    {
        $this->generator = $contentGenerator;
        $this->files = $files;
    }

    /**
     * Sets the template directory.
     *
     * @param $directory
     */
    public function setTemplateDirectory($directory)
    {
        if ($directory == null) {
            // TODO: Load template directory from storage.
        } else {
            $this->templateDirectory = realpath($directory);
        }
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
        $templateDirectory = realpath($this->templateDirectory.'/_template');

        if ($this->files->exists($templateDirectory) && $this->files->isDirectory($templateDirectory)) {
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
        $commonDirectory = realpath($this->templateDirectory.'/_newup/common');

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
        if ($this->getCommonTemplateDirectory() !== null) {
            $this->generator->getRenderer()->addPath($this->getCommonTemplateDirectory());
        }

        $this->generator->addPaths((array)$this->getTemplateDirectory());
        $this->generator->generateContent($this->outputDirectory);
    }

}