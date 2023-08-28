<?php

namespace App\Core\Application\Creator;

use App\Core\Domain\Model\Attachment;
use App\Shared\Infrastructure\ValueObject\UuidValueObject;
use DateTimeImmutable;
use App\Core\Application\Command\Attachment\Create\CreateAttachmentCommand;
use App\Shared\Application\AbstractCreator;
use Illuminate\Http\UploadedFile;

class AttachmentCreator extends AbstractCreator
{
    public static function create(
        UploadedFile $userFile
    ): Attachment
    {
        $fileObject = $userFile;
        $createdAt = new DateTimeImmutable();

        $unixTime = (new DateTimeImmutable())->getTimestamp();
        $fileName = explode(".", $fileObject->getClientOriginalName())[0] . $unixTime;
        $name = md5($fileName);

        $attachment = new Attachment(
            uuid: UuidValueObject::create(),//$command->uuid ? UuidValueObject::create($command->uuid) : UuidValueObject::create(),
            name: $name,
            originName: $fileObject->getClientOriginalName(),
            type: $fileObject->getClientMimeType(),
            createdAt:  $createdAt
        );

        $attachment->addParent($attachment);

        $attachment->addFileType(self::setTypeFile($fileObject));

        $attachment->addUploadFile($fileObject);

        $attachment->addDirPath();

        return $attachment;
    }

    private static function setTypeFile(
        object $fileObject
    ): string
    {
        $pieces = explode("/", $fileObject->getClientMimeType());
        return $pieces[0];
    }

}
