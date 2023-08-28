<?php

namespace Infrastructure\Utils\Exception;

use Exception;
use Illuminate\Validation\ValidationException;

class UnprocessableException extends Exception{
    // Переопределим исключение так, что параметр message станет обязательным
    public function __construct($message, $code = 422, Throwable $previous = null) {
        // некоторый код

        // убедитесь, что все передаваемые параметры верны
        parent::__construct($message, $code, $previous);
    }

    public function getResult(): array {
        return [];
    }
}
