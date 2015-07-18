<?php namespace NewUp\Console\Input;

use NewUp\Exceptions\InvalidPackageTemplateException;
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
     * @throws InvalidPackageTemplateException
     * @param InputDefinition $definition A InputDefinition instance
     */
    public function bind(InputDefinition $definition)
    {
        $this->arguments = array();
        $this->options = array();
        $this->definition = $definition;

        $includePath = $this->getPackageClassPath();

        if (!file_exists($includePath)) {
            throw new InvalidPackageTemplateException("{$includePath} does not exist.");
        }

        include $includePath;

        $packageClass = $this->getNamespacedPackageName();

        if (!class_exists($packageClass)) {
            throw new InvalidPackageTemplateException("{$packageClass} class does not exist.");
        }

        $options = $packageClass::getOptions();
        $arguments = $packageClass::getArguments();

        foreach ($options as $option) {
            $this->definition->addOption(new InputOption(
                $option[0],
                $option[1],
                $option[2],
                $option[3],
                $option[4]
            ));
        }

        foreach ($arguments as $argument) {
            $this->definition->addArgument(new InputArgument(
                $argument[0],
                $argument[1],
                $argument[2],
                $argument[3]
            ));
        }

        $this->parse();
    }

    private function getPackageClassPath()
    {
        return $this->getTemplateArgumentPath() . '_newup/Package.php';
    }

    private function getNamespacedPackageName()
    {
        $path = $this->getTemplateArgumentPath();
        $templateName = json_decode(file_get_contents($path . 'composer.json'))->name;
        $parts = explode('/', $templateName);
        return package_vendor_namespace($parts[0], $parts[1]) . '\Package';
    }

    /**
     * Determines the template path.
     *
     * @return string
     */
    private function getTemplateArgumentPath()
    {
        if ($this->customTemplatePath !== null) {
            return str_finish($this->customTemplatePath, '/');
        }

        // TODO: Return the path from a template in the template storage. Currently only supports direct paths.
    }

}