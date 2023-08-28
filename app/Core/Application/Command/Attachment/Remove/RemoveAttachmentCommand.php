<?php

namespace App\Core\Application\Command\Attachment\Remove;

use App\Core\Application\Command\Attachment\AttachmentCommand;
use App\Shared\Infrastructure\Persistence\Data\UuidData;
use Illuminate\Http\Request;
use Infrastructure\Utils\Exception\UnprocessableException;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\Validation\Required;

class RemoveAttachmentCommand extends AttachmentCommand
{
    /**
     * @param object $ids
     */
    public function __construct(
        #[Required, DataCollectionOf(UuidData::class)] public readonly object $ids,
    ) {}

    public static function fromRequest(
        ?Request $request = null
    ): self
    {
        $ids = ($request->get('ids') !== null) ? collect(
            array_map(fn(string $e): UuidData => UuidData::make($e), explode(",", $request->get('ids')))) : null;

        if(!isset($ids)){
            throw new UnprocessableException('Атрибут `ids` является обязательным.', 422);
        }

        return new self(
            ids:$ids
        );
    }

}
