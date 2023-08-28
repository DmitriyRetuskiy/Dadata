<?php

namespace Infrastructure\Utils\Exception;

use Exception;

class NotFoundException extends Exception
{
    // Переопределим исключение так, что параметр message станет обязательным
    public function __construct($message, $code = 404, Throwable $previous = null) {
        // некоторый код

        // убедитесь, что все передаваемые параметры верны
        parent::__construct($message, $code, $previous);
    }

    public function getResult(): array {
        return [];
    }
}
