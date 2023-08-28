<?php

namespace App\Domain\ValueObjects;

use Exception;
use Ramsey\Uuid\Uuid;

abstract class AbstractId
{
    /**
     * @param string $id
     **/
    public function __construct(
        protected readonly string $id
    )
    {
    }

    /**
     * @param string|null $id
     *
     * @return static
     **/
    public static function create(
        ?string $id = null
    ): static
    {
        return new static(
            $id ?? Uuid::uuid4()->toString()
        );
    }

    /**
     * @return string
     **/
    public function id(): string
    {
        return $this->id;
    }
}
