<?php

namespace NewUp\Console\Input;

use NewUp\Console\Application;
use NewUp\Console\Commands\Templates\Install;
use NewUp\Exceptions\InvalidPackageTemplateException;
use NewUp\Exceptions\TemplatePackageMissingException;
use NewUp\Templates\BasePackageTemplate;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;
use Illuminate\Contracts\Console\Kernel;

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

    /**
     * The package class instance.
     *
     * @var null|BasePackageTemplate
     */
    private $packageClass = null;

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
     * Determines if a user is requesting help information.
     *
     * This is required because we cannot rely on Symfony's
     * help system for the build command. We want to be
     * able to show users help information dynamically
     * because package templates can define their
     * own options and arguments.
     *
     * @return bool
     */
    public function requestingHelpInformation()
    {
        $validHelpTokens = ['-h', '--help'];

        foreach ($this->tokens as $token) {
            if (in_array($token, $validHelpTokens)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Gets the template name from the input tokens.
     *
     * @return string
     */
    public function getTemplateName()
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
     * Gets the package class.
     *
     * @return null|string
     * @throws \NewUp\Exceptions\InvalidPackageTemplateException
     * @throws \NewUp\Exceptions\TemplatePackageMissingException
     */
    private function getPackageClass()
    {
        if ($this->packageClass !== null) {
            return $this->packageClass;
        }

        $includePath = $this->getPackageClassPath();

        if (!file_exists($includePath)) {
            Application::getOutput()->writeln("<comment>{$this->getTemplateName()} is not installed. Attempting to install it now...</comment>");
            $result = app(Kernel::class)->getApplication()->callWithSharedOutput('template:install', ['name' => $this->getTemplateName(), '--confirm' => true]);

            if ($result === Install::INSTALL_FAIL) {
                // Fail angrily.
                throw new TemplatePackageMissingException("The package template {$this->getTemplateName()} is not installed or it cannot be found. The package template must be installed before it can be built.");
            } else {
                Application::getOutput()->writeln('<comment>It looks like everything went well. We will continue building the package template...</comment>');
            }
        }

        scope_include($includePath);

        $this->packageClass = $packageClass = $this->getNamespacedPackageName();

        if (!class_exists($packageClass)) {
            throw new InvalidPackageTemplateException("{$packageClass} class does not exist.");
        }

        return $packageClass;
    }

    /**
     * Binds the current Input instance with the given arguments and options.
     *
     * @throws InvalidPackageTemplateException
     * @throws TemplatePackageMissingException
     *
     * @param InputDefinition $definition A InputDefinition instance
     */
    public function bind(InputDefinition $definition)
    {
        $this->arguments = array();
        $this->options = array();
        $this->definition = $definition;

        $packageClass = $this->getPackageClass();

        $options = $packageClass::getOptions();
        $arguments = $packageClass::getArguments();

        foreach ($options as $option) {
            $this->definition->addOption(new InputOption(
                array_get($option, 0, null),
                array_get($option, 1, null),
                array_get($option, 2, null),
                array_get($option, 3, null),
                array_get($option, 4, null)
            ));
        }

        foreach ($arguments as $argument) {
            $this->definition->addArgument(new InputArgument(
                array_get($argument, 0, null),
                array_get($argument, 1, null),
                array_get($argument, 2, null),
                array_get($argument, 3, null)
            ));
        }

        $this->parse();

        if ((count($options) + count($arguments)) == 0 &&
            count($this->arguments) == 2
        ) {
            // The package does not define any arguments or options. We
            // will check to see if the user has not supplied all of
            // the required arguments specified by NewUp. If they
            // haven't, it is most like the install directory.
            // We will add that argument automatically here
            // to make general usage a little bit nicer.
            $this->arguments = array_merge($this->arguments, ['newup-output-directory' => '.']);
        }

    }

    /**
     * Gets the path to the new package class.
     *
     * @return string
     */
    private function getPackageClassPath()
    {
        return $this->getTemplateArgumentPath() . '_newup/Package.php';
    }

    /**
     * Gets the namespaced package name.
     *
     * @return string
     */
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

        return str_finish(find_tse_template($this->getTemplateName()), '/');
    }

    public function getTokens()
    {
        return $this->tokens;
    }

}