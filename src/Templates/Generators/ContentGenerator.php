<?php

namespace NewUp\Templates\Generators;

use Illuminate\Support\Str;
use NewUp\Contracts\Templates\Renderer;
use NewUp\Filesystem\Filesystem;

/**
 * Class ContentGenerator
 *
 * The ContentGenerator is a facade that helps manage the interactions of
 * the path generators and the actual template generation. Using it is for
 * the most part exactly the same as using the `PathManager`.
 *
 * - To generate only the directory structure, call the 'emitStructure()` method
 * - To generate both the directory structure and file contents, call the `generateContent()` method
 *
 * @package NewUp\Templates\Generators
 */
class ContentGenerator
{

    /**
     * The PathManager instance.
     *
     * @var PathManager
     */
    protected $pathManager = null;

    /**
     * The Filesystem implementation instance.
     *
     * @var Filesystem
     */
    protected $fileSystem = null;

    /**
     * Determines if the engine is running inside the template directory.
     *
     * @var bool
     */
    protected $insideTemplateDirectory = false;

    /**
     * A collection of patterns to match to determine if NewUp should simply copy a file.
     *
     * @var array
     */
    protected $copyVerbatimPatterns = [];

    /**
     * A collection of files and patterns to process anyway.
     *
     * The $copyVerbatim collection allows template authors
     * to simply copy files to the output directory based
     * on a given pattern. However, sometimes it might
     * be necessary to process a given file that
     * could be matched by one of the patterns.
     * Add those files/patterns to this list
     * to have them processed anyways.
     *
     * @var array
     */
    protected $copyVerbatimExcludePatterns = [];

    public function __construct(PathManager $pathManager, Filesystem $fileSystem)
    {
        $this->pathManager = $pathManager;
        $this->fileSystem  = $fileSystem;
    }

    /**
     * Sets whether or not the engine is running inside a '_template' directory.
     *
     * @param $inside
     */
    public function setInsideTemplateDirectory($inside)
    {
        $this->insideTemplateDirectory = $inside;
        $this->getPathManager()->getGenerator()->setInsideTemplateDirectory($inside);
    }

    /**
     * Returns the PathManager instance.
     *
     * @return PathManager
     */
    public function getPathManager()
    {
        return $this->pathManager;
    }

    /**
     * Returns the Renderer implementation instance.
     *
     * @return Renderer
     */
    public function getRenderer()
    {
        return $this->pathManager->getRenderer();
    }

    /**
     * Generates files and contents in the provided destination directory.
     *
     * @param  $destination
     * @return array
     */
    public function generateContent($destination)
    {
        $pathsWrittenTo = [];

        $packageStructure = $this->pathManager->emitStructure($destination);

        // At this point, since the file structure has been emitted we can add the
        // PathManager's file name collector to the template renderer so that the
        // 'path' (and other) functions work as expected.
        $this->getRenderer()->addCollector($this->getPathManager()->getCollector());
        $this->getRenderer()->addCollector(app('NewUp\Templates\Renderers\Collectors\InputCollector'));
        
        $this->pathManager->getRenderer()->setIgnoreUnloadedTemplateErrors(true);

        foreach ($packageStructure as $packageFile) {
            if ($this->fileSystem->exists($packageFile['full'])) {
                if (!$this->shouldCopyFileInstead($packageFile['original'])) {
                    $packageFileContents = $this->pathManager->getRenderer()->render($packageFile['original']);

                    if ($packageFileContents != null && strlen($packageFileContents) > 0) {
                        $this->fileSystem->put($packageFile['full'], $packageFileContents);
                        $pathsWrittenTo[] = $packageFile;
                    }
                } else {
                    // Copy the file instead.
                    $this->fileSystem->copy($packageFile['origin'], $packageFile['full']);
                }
            }
        }

        $this->pathManager->getRenderer()->setIgnoreUnloadedTemplateErrors(false);

        return $pathsWrittenTo;
    }

    /**
     * Sets the verbatim patterns.
     *
     * @param $patterns
     */
    public function setVerbatimPatterns($patterns)
    {
        $this->copyVerbatimPatterns = $patterns;
    }

    /**
     * Sets the verbatim exclude patterns.
     *
     * @param $patterns
     */
    public function setVerbatimExcludePatterns($patterns)
    {
        $this->copyVerbatimExcludePatterns = $patterns;
    }

    /**
     * Determines if a file should be excluded copying.
     *
     * @param  $file
     * @return bool
     */
    private function isExcludedFromVerbatim($file)
    {
        foreach ($this->copyVerbatimExcludePatterns as $pattern) {
            if (Str::is($pattern, $file)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determines if a file should simply be copied.
     *
     * @param $file
     * @return bool
     */
    private function shouldCopyFileInstead($file)
    {
        // Check if it should not be processed.
        if ($this->isExcludedFromVerbatim($file)) {
            return false;
        }

        foreach ($this->copyVerbatimPatterns as $pattern) {
            if (Str::is($pattern, $file)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Handle dynamic calls to the underlying PathManager instance.
     *
     * @param $method
     * @param $args
     * @return mixed
     */
    public function __call($method, $args)
    {
        switch (count($args)) {
            case 0:
                return $this->pathManager->$method();
            case 1:
                return $this->pathManager->$method($args[0]);
            case 2:
                return $this->pathManager->$method($args[0], $args[1]);
            case 3:
                return $this->pathManager->$method($args[0], $args[1], $args[2]);
            case 4:
                return $this->pathManager->$method($args[0], $args[1], $args[2], $args[3]);
            default:
                return call_user_func(array($this->pathManager, $method), $args);
        }
    }

}