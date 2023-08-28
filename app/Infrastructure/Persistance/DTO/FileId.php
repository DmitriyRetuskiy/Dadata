<?php

namespace App\Infrastructure\Persistance\DTO;

use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Attributes\Validation\Uuid;

class FileId extends AbstractData {

    /**
     * @param string|null $id
     **/
    public function __construct(
        #[StringType, Uuid] public readonly ? string $id
    ) {}

    static public function create(string $id): FileId
    {
        return new FileId($id);
    }
}
