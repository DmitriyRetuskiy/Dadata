<?php

namespace App\Core\Application\Command\Attachment\Create;

use App\Shared\Infrastructure\Persistence\Data\AbstractData;
use Illuminate\Http\UploadedFile;
use JetBrains\PhpStorm\Pure;
use phpDocumentor\Reflection\File;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Uuid;

class CreateMaskCommand extends AbstractData
{
    // объявить все в кострукторе
    public function __construct(
        #[Nullable] public readonly ?string $colorMask1 = null,
        #[Nullable] public readonly ?string $colorMask2 = null,
        #[Nullable] public readonly ?string $nameMethod1 = null,
        #[Nullable] public readonly ?string $nameMethod2 = null,
        #[Nullable] public readonly ?UploadedFile $fileBase = null,
        #[Nullable] public readonly ?UploadedFile $fileMask1 = null,
        #[Nullable] public readonly ?UploadedFile $fileMask2 = null,
        #[Nullable] public readonly ?UploadedFile $fileReflection = null,
        #[Nullable] public readonly ?UploadedFile $result = null
    )
    {

    }

    public static function fromMaskRequest(object $request): self
    {
//        return new self(formDate: $request->all('formDate'));
        return new self(
            colorMask1: $request->input('colorMask1') ?? null,
            colorMask2: $request->input('colorMask2') ?? null,
            nameMethod1: $request->input('nameMethod1') ?? null,
            nameMethod2: $request->input('nameMethod2') ?? null,
            fileBase: $request->file('fileBase') ?? null,
            fileMask1: $request->file('fileMask1') ?? null,
            fileMask2: $request->file('fileMask2') ?? null,
            fileReflection: $request->file('fileReflection') ?? null
        );
    }

    public static function fromMaskFile($path): self
    {
        return new self(
            result: $path ?? null
        );
    }

}
