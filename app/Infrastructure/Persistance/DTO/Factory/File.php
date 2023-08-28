<?php

namespace App\Infrastructure\Persistance\DTO\Factory;

use App\Infrastructure\Persistance\DTO\RequestDto;

class File
{
    /**
     * @param object $request
     *
     * @return RequestDto
     *
     */
    public static function fromRequest(
        object $request
    ): RequestDto
    {
        return new RequestDto(
            id: null,
            userFile: $request->file('userFile') ? $request->file('userFile') : null
        );
    }

}
