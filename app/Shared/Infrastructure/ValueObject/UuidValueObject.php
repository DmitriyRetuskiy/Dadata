<?php

namespace App\Shared\Infrastructure\ValueObject;

use Ramsey\Uuid\Uuid;

class UuidValueObject
{
    /**
     * @param string $uuid
     **/
    public function __construct(
        protected readonly string $uuid
    )
    {
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->uuid;
    }

    /**
     * @param string|null $uuid
     *
     * @return UuidValueObject
     **/
    public static function create(
        ?string $uuid = null
    ): static
    {
        return new static(
            $uuid ?? Uuid::uuid4()->toString()
        );
    }

    /**
     * @return string
     **/
    public function uuid(): string
    {
        return $this->uuid;
    }
}
