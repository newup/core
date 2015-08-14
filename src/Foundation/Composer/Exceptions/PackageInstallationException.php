<?php

namespace NewUp\Foundation\Composer\Exceptions;


class PackageInstallationException extends ComposerException {

    /**
     * The Composer process error output.
     *
     * @var string
     */
    protected $composerErrorOutput = '';

    public function __construct($composerErrorOutput, $message = null, $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->composerErrorOutput = $composerErrorOutput;
    }

    /**
     * Gets the Composer process error output.
     *
     * @return string
     */
    public function getErrorOutput()
    {
        return $this->composerErrorOutput;
    }

}