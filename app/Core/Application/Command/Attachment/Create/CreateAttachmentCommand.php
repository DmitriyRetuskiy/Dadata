<?php

namespace App\Core\Application\Command\Attachment\Create;

use App\Core\Application\Command\Attachment\AttachmentCommand;
use App\Infrastructure\Persistance\DTO\RequestDto;
use App\Shared\Infrastructure\Persistence\Data\AbstractData;
use App\Shared\Infrastucture\Persistence\Data\AttachmentData;
use Illuminate\Http\UploadedFile;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\Validation\File;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Attributes\Validation\Uuid;

class CreateAttachmentCommand extends AbstractData
{
    /**
     * @param string|null $uuid
     * //* @param object|null $userFile
     */
    public function __construct(
        #[Nullable, Uuid] public readonly ?string $uuid,
        #[Nullable, DataCollectionOf(UploadedFile::class)] public readonly ?object $userFile
    )
    {
    }

//    public function toArray(): array
//    {
//        return [
//            'uuid'=> $this->uuid,
//            'userFile' => (array)$this->userFile
//        ];
//    }

    /**
     * @param object $request
     *
     * @return CreateAttachmentCommand
     */
    public static function fromRequest(
        object $request
    ): self
    {
        return new self(
            uuid: null,
            userFile: $request->file('userFile') ? $request->file('userFile') /*collect(array_map(fn(object $e): UploadedFile => $e, $request->file('userFile')))*/ : null
        );
    }

}
