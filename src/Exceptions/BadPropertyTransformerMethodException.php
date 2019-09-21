<?php declare(strict_types=1);

namespace Nahid\Presento\Exceptions;


class BadPropertyTransformerMethodException extends \Exception
{
    public function __construct($method, $code = 400, \Throwable $previous = null)
    {
        $message = sprintf("Your given method %s is not exists!", $method);
        parent::__construct($message, $code, $previous);
    }
}