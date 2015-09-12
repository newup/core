<?php

namespace NewUp\Templates\Generators;

use NewUp\Contracts\IO\FileTreeGenerator;
use NewUp\Contracts\Templates\PathNameParser;
use NewUp\Contracts\Templates\Renderer;
use NewUp\Filesystem\DirectoryAnalyzer;
use NewUp\Configuration\Parsers\YAMLParser;
use NewUp\Templates\Renderers\Collectors\FileNameCollector;
use NewUp\Filesystem\Generators\PathTreeArrayFormat;

/**
 * Class PathManager
 *
 * The PathManager is a simple-to-use facade that makes working with the following
 * sub-systems easier when generating a package's directory and file structure:
 *
 * - NewUp\Templates\Analyzers\DirectoryAnalyzer
 * - NewUp\Contracts\Templates\PathNameParser
 * - NewUp\Contracts\IO\FileTreeGenerator
 *
 * This manager does NOT add any content to the files it creates.
 *
 * Paths to the template package must be added using the `addPaths()` method.
 *
 * Data can be added to the underlying FileNameCollector by using the `addPathToCollector()`
 * and `addRawToCollector()` methods. All data added to the collector will be considered when
 * the final directory structure is created.
 *
 * To generate the directory structure in a directory, use the `emitStructure($destination)`
 * method where the `$destination` is the directory in which the structure will be created.
 *
 * @package NewUp\Templates\Generators
 */
class PathManager
{

    use PathTreeArrayFormat;

    /**
     * The FileTreeGenerator instance.
     *
     * @var FileTreeGenerator
     */
    protected $treeGenerator;

    /**
     * The TemplateRenderer instance.
     *
     * @var Renderer
     */
    protected $templateRenderer;

    /**
     * Just a file name collector instance.
     *
     * @var FileNameCollector
     */
    protected $filePathCollector;

    /**
     * The DirectoryAnalyzer instance.
     *
     * @var DirectoryAnalyzer
     */
    protected $analyzer;

    /**
     * The PathNameParser instance.
     *
     * @var PathNameParser
     */
    protected $parser;

    /**
     * The YAML Parser.
     *
     * @var YAMLParser
     */
    protected $yaml;

    /**
     * Holds current PathCollector data.
     *
     * @var array
     */
    protected $currentCollectorData = [];

    /**
     * An array of paths.
     *
     * @var array
     */
    protected $paths = [];

    /**
     * Returns a new PathManager instance.
     *
     * @param  FileTreeGenerator $treeGenerator
     * @param  Renderer          $renderer
     * @param  DirectoryAnalyzer $analyzer
     * @param  PathNameParser    $parser
     */
    public function __construct(
        FileTreeGenerator $treeGenerator,
        Renderer $renderer,
        DirectoryAnalyzer $analyzer,
        PathNameParser $parser
    ) {
        $this->treeGenerator    = $treeGenerator;
        $this->templateRenderer = $renderer;
        $this->analyzer         = $analyzer;
        $this->parser           = $parser;

        $this->filePathCollector = new FileNameCollector;
        $this->yaml              = new YAMLParser;
    }

    /**
     * Returns the FileTreeGenerator instance.
     * 
     * @return FileTreeGenerator
     */
    public function getGenerator()
    {
        return $this->treeGenerator;
    }

    /**
     * Returns the Renderer implementation instance.
     *
     * @return Renderer
     */
    public function getRenderer()
    {
        return $this->templateRenderer;
    }

    /**
     * Adds a path to the path data collector.
     *
     * @param $path
     */
    public function addPathToCollector($path)
    {
        $this->filePathCollector->addFileNames($this->yaml->parseFile($path));
    }

    /**
     * Adds a raw set of collector replacement values to the path data collector.
     *
     * @param $key
     * @param $value
     */
    public function addRawToCollector($key, $value)
    {
        $this->filePathCollector->addFileNames([$key => $value]);
    }

    /**
     * Adds paths to the underlying collectors and tree generators.
     *
     * @param array $paths
     * @throws \NewUp\Exceptions\InvalidPathException
     */
    public function addPaths(array $paths)
    {
        $this->currentCollectorData = $this->filePathCollector->collect();

        foreach ($paths as $path) {
            $this->addPath($path);
        }
    }

    /**
     * Adds a single path to the underlying collectors and tree generators.
     *
     * This method will add all processed paths to the internal collection.
     * Additionally, it will return the array of paths that were processed
     * during the current invocation.
     *
     * @param  $path
     * @throws \NewUp\Exceptions\InvalidPathException
     * @return array
     */
    private function addPath($path)
    {
        $paths = $this->analyzer->analyze($path);

        $newPaths = [];

        foreach ($paths as $filePath) {
            $newPathInformation             = $filePath;
            $newPathInformation['original'] = $filePath['path'];

            if ($this->getCollectorValue($filePath['path']) !== null) {
                // If the value from the collector is not null, we need to process the path value
                // using the template renderer.
                $newPathInformation['path'] = $this->templateRenderer
                    ->renderString($this->getCollectorValue($filePath['path']));
            } else {
                // If the value IS null, we need to process the path value using the path parser.
                $newPathInformation['path'] = $this->parser->processPath($filePath['path']);
            }

            $newPaths[]    = $newPathInformation;
            $this->paths[] = $newPathInformation;

            // Add the new file path association to the collector.
            $this->addRawToCollector($filePath['path'], $newPathInformation['path']);
        }

        $this->templateRenderer->addPath($path);

        return $newPaths;
    }

    /**
     * Returns the value stored in the path collector for the given path.
     *
     * @param $key
     * @return null|mixed
     */
    private function getCollectorValue($key)
    {
        if (isset($this->currentCollectorData['sys_pathNames'])) {
            if (array_key_exists($key, $this->currentCollectorData['sys_pathNames'])) {
                return $this->currentCollectorData['sys_pathNames'][$key];
            }
        }

        return null;
    }

    /**
     * Creates a directory structure in the provided destination directory.
     *
     * @param $destination
     */
    public function emitStructure($destination)
    {
        $this->treeGenerator->addPaths($this->getPaths());

        return $this->treeGenerator->generate($destination);
    }

    /**
     * A helper method to quickly emit the directory structure from
     * either a single file or an array of files to a single output
     * directory.
     *
     * @param       $from
     * @param       $to
     * @param array $collectorData
     * @return PathManager
     */
    public static function copy($from, $to, $collectorData = [])
    {
        $manager = app(get_called_class());

        if (!is_array($from)) {
            $from = array($from);
        }

        // Iterate over the $collectorData and add any data to the
        // $manager that might be required.
        foreach ($collectorData as $key => $value) {
            // A value of NULL will indicate that the given key should
            // be added to the collector's paths.
            if ($value == null) {
                $manager->addPathToCollector($key);
            } else {
                $manager->addRawToCollector($key, $value);
            }
        }

        $manager->addPaths($from);
        $manager->emitStructure($to);

        return $manager;
    }

    /**
     * Returns all the processed paths.
     *
     * @return array
     */
    public function getPaths()
    {
        return $this->paths;
    }

    /**
     * Returns the file name collector instance.
     *
     * @return FileNameCollector
     */
    public function getCollector()
    {
        return $this->filePathCollector;
    }

}