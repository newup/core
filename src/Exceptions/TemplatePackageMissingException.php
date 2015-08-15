<?php

namespace NewUp\Exceptions;

class TemplatePackageMissingException extends NewUpException
{

    public function __construct($message = null, $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}