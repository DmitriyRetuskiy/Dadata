<?php

namespace App\Shared\Infrastructure\Persistence\Data;

use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Attributes\Validation\Uuid;

class UuidData extends AbstractData
{
    /**
     * @param string|null $uuid
     */
    public function __construct(
        #[StringType, Uuid] public readonly ?string $uuid
    )
    {
    }

    /**
     * @param string $uuid
     *
     * @return self
     */
    public static function make(
        string $uuid
    ): self
    {
        return new self(
            uuid: $uuid
        );
    }

}
