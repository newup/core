<?php

namespace NewUp\Templates\Renderers;

use Illuminate\Support\Str;
use NewUp\Contracts\DataCollector;
use NewUp\Contracts\Templates\Filter as FilterContract;
use NewUp\Contracts\Templates\Renderer;
use NewUp\Contracts\Templates\RendererFunction;
use NewUp\Exceptions\InvalidPathException;
use NewUp\Foundation\Application;

class TemplateRenderer implements Renderer
{
    /**
     * Twig file system loader instance.
     *
     * @var null|\Twig_Loader_Filesystem
     */
    protected $twigFileLoader = null;

    /**
     * Twig array loader.
     *
     * Holds system templates (they are given a custom name).
     *
     * @var null|\Twig_Loader_Array
     */
    protected $twigSystemLoader = null;

    /**
     * The Twig environment instance.
     *
     * Used by the `render()` function.
     *
     * @var null|\Twig_Environment
     */
    protected $twigEnvironment = null;

    /**
     * The Twig environment instance.
     *
     * Used by the `renderString()` function.
     *
     * @var null|\Twig_Environment
     */
    protected $twigStringEnvironment = null;

    /**
     * Renderer environment variables.
     *
     * @var array
     */
    protected $dataArray = [];

    /**
     * The Twig environment options.
     *
     * @var array
     */
    protected $environmentOptions = [
        'autoescape' => false
    ];

    /**
     * An array of data collectors.
     *
     * @var array
     */
    protected $dataCollectors = [];

    protected $ignoreUndefinedTemplateErrors = false;

    /**
     * Returns a new instance of TemplateRenderer
     */
    public function __construct()
    {
        $this->twigFileLoader   = new \Twig_Loader_Filesystem();
        $this->twigSystemLoader = new \Twig_Loader_Array([
            'template' => load_system_template('TemplateClass')
        ]);

        $this->twigEnvironment       =
            new \Twig_Environment(new \Twig_Loader_Chain([$this->twigSystemLoader, $this->twigFileLoader]),
                $this->environmentOptions);
        $this->twigStringEnvironment =
            new \Twig_Environment(new \Twig_Loader_Chain([
                $this->twigSystemLoader,
                $this->twigFileLoader,
                new \Twig_Loader_String()
            ]), $this->environmentOptions);

        $this->registerFilters();
        $this->registerFunctions();
        $this->registerCorePaths();
    }

    /**
     * Registers the core system paths.
     *
     * @throws InvalidPathException
     */
    private function registerCorePaths()
    {
        $this->addPath(core_templates_path());
    }

    /**
     * Registers the template filters.
     */
    private function registerFilters()
    {
        $filters = config('app.render_filters', []);

        foreach ($filters as $filter) {
            $filter = app($filter);

            if ($filter instanceof FilterContract) {
                $this->twigEnvironment->addFilter(new \Twig_SimpleFilter($filter->getName(), $filter->getFilter()));
                $this->twigStringEnvironment->addFilter(new \Twig_SimpleFilter($filter->getName(),
                    $filter->getFilter()));
            }
        }
    }

    private function registerFunctions()
    {
        $functions = config('app.render_functions', []);

        foreach ($functions as $function) {
            $function = app($function);

            if ($function instanceof RendererFunction) {
                $function->setContext($this);
                $this->twigEnvironment->addFunction($function->getFunction());
                $this->twigStringEnvironment->addFunction($function->getFunction());
            }
        }
    }

    /**
     * Adds a path to the template environment.
     *
     * @param $path
     * @throws InvalidPathException
     */
    public function addPath($path)
    {
        try {
            $this->twigFileLoader->addPath($path);
        } catch (\Twig_Error_Loader $e) {
            throw new InvalidPathException($e->getMessage());
        }
    }

    /**
     * Gets the paths of the rendering environment.
     *
     * @return mixed
     */
    public function getPaths()
    {
        return $this->twigFileLoader->getPaths();
    }


    /**
     * Returns the file loader.
     *
     * @return null|\Twig_Loader_Filesystem
     */
    public function getLoader()
    {
        return $this->twigFileLoader;
    }

    /**
     * Returns the underlying Twig environment.
     *
     * @return null|\Twig_Environment
     */
    public function getEnvironment()
    {
        return $this->twigEnvironment;
    }

    /**
     * Returns the underlying Twig environment used by renderString().
     *
     * @return null|\Twig_Environment
     */
    public function getStringEnvironment()
    {
        return $this->twigStringEnvironment;
    }

    /**
     * Gets the environment data.
     *
     * @return array
     */
    public function getData()
    {
        $systemInformation                  = [];
        $systemInformation['newup_version'] = Application::VERSION;

        return $this->dataArray + $systemInformation + $this->collectData();
    }

    /**
     * Sets an environment variable.
     *
     * @param $key
     * @param $value
     */
    public function setData($key, $value)
    {
        $this->dataArray[$key] = $value;
    }

    /**
     * Renders a template by name.
     *
     * @param $templateName
     * @return string
     * @throws InvalidTemplateException
     * @throws InvalidSyntaxException
     * @throws RuntimeException
     * @throws SecurityException
     */
    public function render($templateName)
    {
        try {
            return $this->twigEnvironment->render($templateName, $this->getData());
        } catch (SecurityException $e) {
            throw new SecurityException($e->getMessage(), $e->getCode(), $e);
        } catch (\Twig_Error_Runtime $e) {
            throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
        } catch (\Twig_Error_Syntax $e) {
            throw new InvalidSyntaxException($e->getMessage(), $e->getCode(), $e);
        } catch (\Twig_Error_Loader $e) {

            if ($this->ignoreUndefinedTemplateErrors && Str::contains($e->getMessage(), 'is not defined'))
            {
                return;
            }

            throw new InvalidTemplateException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Renders a string as a template.
     *
     * @param $templateString
     * @return string
     * @throws InvalidTemplateException
     * @throws InvalidSyntaxException
     * @throws RuntimeException
     * @throws SecurityException
     */
    public function renderString($templateString)
    {
        try {
            return $this->twigStringEnvironment->render($templateString, $this->getData());
        } catch (SecurityException $e) {
            throw new SecurityException($e->getMessage(), $e->getCode(), $e);
        } catch (\Twig_Error_Runtime $e) {
            throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
        } catch (\Twig_Error_Syntax $e) {
            throw new InvalidSyntaxException($e->getMessage(), $e->getCode(), $e);
        } catch (\Twig_Error_Loader $e) {

            if ($this->ignoreUndefinedTemplateErrors && Str::contains($e->getMessage(), 'is not defined'))
            {
                return;
            }

            throw new InvalidTemplateException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Adds a data collector to the list of data collectors.
     *
     * @param DataCollector $collector
     */
    public function addCollector(DataCollector $collector)
    {
        $this->dataCollectors[] = $collector;
    }

    /**
     * Gets the list of data collectors.
     *
     * @return array
     */
    public function getCollectors()
    {
        return $this->dataCollectors;
    }

    /**
     * Collects all data from data collectors.
     *
     * @return array
     */
    public function collectData()
    {
        $collectedData = [];

        foreach ($this->dataCollectors as $collector) {
            $collectedData = $collectedData + $collector->collect();
        }

        return $collectedData;
    }

    /**
     * Sets whether or not undefined template errors will be reported.
     *
     * @param $doIgnore
     */
    public function setIgnoreUnloadedTemplateErrors($doIgnore)
    {
        $this->ignoreUndefinedTemplateErrors = $doIgnore;
    }

}