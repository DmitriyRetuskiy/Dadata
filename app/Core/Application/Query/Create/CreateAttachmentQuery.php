<?php

namespace App\Core\Application\Query\Create;

use App\Core\Application\Query\AttachmentQuery;
use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Attributes\Validation\Uuid;

class CreateAttachmentQuery extends AttachmentQuery
{
    /**
     * @param string|null $uuid
     * @param string|null $ids
     * @param int|null $offset
     */
    public function __construct(
        #[StringType, Uuid] public readonly ?string $uuid,
        #[Nullable, StringType] public readonly ?string $ids,
        #[Nullable, IntegerType] public readonly ?int $offset,
    ) {}

    #[Pure] public static function fromRequest(
        ?string $uuid = null,
        ?string $identifiers = null,
        ?int $offset = null
    ): self
    {
        return new self(
            uuid: $uuid,
            ids: $identifiers,
            offset: $offset
        );
    }

}
