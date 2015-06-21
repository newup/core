<?php namespace NewUp\Console\Input;

use NewUp\Templates\Parsers\YAMLParser;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;

/**
 * GeneratorInput extends Symfony's ArgvInput.
 *
 * It's general usage is the same as ArgvInput. However, it will inspect
 * the command arguments/options to determine a path where the command can
 * locate additional commands and parameters to use. This is useful when
 * building templates that accept arguments and options.
 *
 * For example, a package template can be build to generate a Laravel 4
 * package:
 *
 * newup a newup/laravel4-package --resources
 *
 * Generally, this would throw a 'RuntimeException' because 'resources' is
 * not defined as on option and any NewUp command.
 *
 * @package NewUp\Console\Input
 */
class GeneratorInput extends ArgvInput
{

    private $tokens;
    private $parsed;

    private $yamlParser;

    /**
     * The template name.
     *
     * @var string
     */
    private $templateName = null;

    /**
     * The custom template path.
     *
     * @var string|null
     */
    private $customTemplatePath = null;

    public function __construct(array $argv = null, InputDefinition $definition = null)
    {
        if (null === $argv) {
            $argv = $_SERVER['argv'];
        }

        array_shift($argv);
        $this->tokens = $argv;

        $this->yamlParser = new YAMLParser;

        $this->getTemplateName();
        $this->initializeTemplatePath();

        parent::__construct($definition);
    }

    /**
     * Gets the template name from the input tokens.
     *
     * @return string
     */
    private function getTemplateName()
    {
        if ($this->templateName == null) {
            // The template name should be the second token.
            $this->templateName = $this->tokens[1];
        }

        return $this->templateName;
    }

    /**
     * Initializes the custom template path variable.
     */
    private function initializeTemplatePath()
    {
        $this->customTemplatePath = null;

        foreach ($this->tokens as $token) {
            if (str_is('--newup-directory*', $token)) {
                $this->customTemplatePath = substr($token, 18);
                break;
            }
        }
    }

    /**
     * Binds the current Input instance with the given arguments and options.
     *
     * @param InputDefinition $definition A InputDefinition instance
     */
    public function bind(InputDefinition $definition)
    {
        $this->arguments  = array();
        $this->options    = array();
        $this->definition = $definition;

        $this->bindTemplateArgumentsAndOptions($this->getTemplateArgumentPath());
        $this->parse();
    }

    /**
     * Binds the template arguments and options.
     *
     * @param $path
     */
    private function bindTemplateArgumentsAndOptions($path)
    {
        if (file_exists($path)) {
            $customInputs = $this->yamlParser->parseFile($path);
            $this->processOptions($customInputs);
            $this->processArguments($customInputs);
        }
    }

    /**
     * Determines the template path.
     *
     * @return string
     */
    private function getTemplateArgumentPath()
    {
        if ($this->customTemplatePath !== null) {
            return str_finish($this->customTemplatePath, '/') . '_newup/argv.yaml';
        }

        // TODO: Return the path from a template in the template storage. Currently only supports direct paths.
    }

    /**
     * Gathers all custom options and adds them to the command definition.
     *
     * @param $inputs
     */
    private function processOptions($inputs)
    {
        $options = array_get($inputs, 'options', []);

        foreach ($options as $option) {
            $this->addCustomOption($option);
        }
    }

    /**
     * Gathers all custom arguments and adds them to the command definition.
     *
     * @param $inputs
     */
    private function processArguments($inputs)
    {
        $arguments = array_get($inputs, 'arguments', []);

        foreach ($arguments as $argument) {
            $this->addCustomArgument($argument);
        }
    }

    /**
     * Gets the Symfony option mode integer for the given string.
     *
     * @param  $optionModeString
     * @return int
     */
    private function getOptionMode($optionModeString)
    {
        if ($optionModeString == null) {
            return InputOption::VALUE_NONE;
        }

        $optionModeString = str_replace(' ', '', $optionModeString);
        $chars            = str_split($optionModeString);

        $mode = null;

        foreach ($chars as $char) {
            switch ($char) {
                case 'r':
                    $mode = $mode | InputOption::VALUE_REQUIRED;
                    break;
                case 'o':
                    $mode = $mode | InputOption::VALUE_OPTIONAL;
                    break;
                case 'a':
                    $mode = $mode | InputOption::VALUE_IS_ARRAY;
                    break;
                case 'n':
                    $mode = $mode | InputOption::VALUE_NONE;
                    break;
            }
        }

        return $mode;
    }

    /**
     * Gets the Symfony argument mode integer for the given string.
     *
     * @param  $argumentModeString
     * @return int
     */
    private function getArgumentMode($argumentModeString)
    {
        if ($argumentModeString == null) {
            return InputArgument::OPTIONAL;
        }

        $argumentModeString = str_replace(' ', '', $argumentModeString);
        $chars              = str_split($argumentModeString);

        $mode = null;

        foreach ($chars as $char) {
            switch ($char) {
                case 'r':
                    $mode = $mode | InputArgument::REQUIRED;
                    break;
                case 'o':
                    $mode = $mode | InputArgument::OPTIONAL;
                    break;
                case 'a':
                    $mode = $mode | InputArgument::IS_ARRAY;
                    break;
            }
        }

        return $mode;
    }

    /**
     * Gets a command/option mode.
     *
     * @param $data
     * @return mixed
     */
    private function getCustomMode($data)
    {
        return array_get($data, 'mode', null);
    }

    /**
     * Gets a command/option description.
     *
     * @param $data
     * @return mixed
     */
    private function getCustomDescription($data)
    {
        return array_get($data, 'description', '');
    }

    /**
     * Gets an option shortcut.
     *
     * @param $data
     * @return mixed
     */
    private function getCustomShortcut($data)
    {
        return array_get($data, 'shortcut', null);
    }

    /**
     * Gets a command/option default value.
     *
     * @param $data
     * @return mixed
     */
    private function getCustomDefault($data)
    {
        $defaultValue = array_get($data, 'default', null);

        if ($defaultValue !== null)
        {
            if ($defaultValue == 'null')
            {
                return null;
            }
        }

        return $defaultValue;
    }

    /**
     * Gets a command/option name.
     *
     * @throws \RuntimeException
     * @param  $data
     * @return mixed
     */
    private function getCustomName($data)
    {
        $name = array_get($data, 'name', null);

        if ($name == null) {
            throw new \RuntimeException('Custom arguments and options must have a name.');
        }

        return $name;
    }

    /**
     * Adds a custom option to the input.
     *
     * @param $optionData
     */
    private function addCustomOption($optionData)
    {
        $name        = $this->getCustomName($optionData);
        $shortcut    = $this->getCustomShortcut($optionData);
        $mode        = $this->getOptionMode($this->getCustomMode($optionData));
        $description = $this->getCustomDescription($optionData);
        $default     = $this->getCustomDefault($optionData);

        $this->definition->addOption(new InputOption(
            $name,
            $shortcut,
            $mode,
            $description,
            $default
        ));
    }

    /**
     * Adds a custom argument to the input.
     *
     * @param $argumentData
     */
    private function addCustomArgument($argumentData)
    {
        $name        = $this->getCustomName($argumentData);
        $mode        = $this->getArgumentMode($this->getCustomMode($argumentData));
        $description = $this->getCustomDescription($argumentData);
        $default     = $this->getCustomDefault($argumentData);

        $this->definition->addArgument(new InputArgument(
            $name,
            $mode,
            $description,
            $default
        ));
    }

}