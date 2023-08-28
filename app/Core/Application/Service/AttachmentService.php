<?php

namespace App\Core\Application\Service;

use App\Core\Application\Service\ImageService;
use App\Core\Application\Creator\AttachmentCreator;
use App\Core\Domain\Model\Attachment;
use App\Models\AttachmentModel;
use DateTimeImmutable;
use App\Application\FileManager;
use App\Core\Application\Command\Attachment\Create\CreateAttachmentCommand;
use App\Domain\Entity\FileOrigin;
use App\Domain\ValueObjects\FileId;
use App\Infrastructure\Persistance\DTO\FileId as FileIdDTO;
use App\Infrastructure\Persistance\DTO\RequestDto;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Infrastructure\Utils\Exception\NotFoundException;
use Infrastructure\Utils\Exception\UnprocessableException;

class AttachmentService extends ImageService
{

    private static function insertAttachmentToDatabase(Attachment $attachment): void // inserted/fail
    {
        AttachmentModel::insertDateTable($attachment);
        if ($attachment->thumbnails()) {
            foreach ($attachment->thumbnails() as $obj) {
                AttachmentModel::insertDateTable($obj);
            }
        }
    }

    public function uploadFileByType(
        UploadedFile $userFile
    ): Attachment
    {

        if (!isset($userFile)) {
            throw new NotFoundException('Файл отсутствует');
        }

        $attachment = self::createAttachment($userFile);

        $attachmentFinal = match ($attachment->fileType()) {
            'image' => $this->uploadFileImage($attachment),
            default => $this->uploadFile($attachment)
        };

        //загрузить файлы в базу данных подключить
//        self::insertAttachmentToDatabase($attachment);
        return $attachment;
    }

    private static function createAttachment(
        UploadedFile $userFile
    ): Attachment
    {

        $attachment = AttachmentCreator::create(
            $userFile
        );

        return $attachment;
    }

    public function deleteFileFromStorage(Attachment $attachment)
    {

        foreach ($attachment->thumbnails() as $thumbnail) {
            //var_dump($thumbnail->path());
            Storage::disk('public')->delete($attachment->path());
        }
        //var_dump($attachment->path());
        //var_dump($attachment->checkDirectory());
        $dir = $attachment->checkDirectory();
        Storage::disk('public')->delete($attachment->path());
        $this->recursiveCheckEmptyDirectory($dir);
    }

    public function recursiveCheckEmptyDirectory($dir)
    {
        Storage::allFiles($dir);

        if (empty(Storage::allFiles($dir))) {
            Storage::deleteDirectory($dir);

            $dirs = preg_split('/\//', $dir);
            array_pop($dirs);
            $dir = implode("/", $dirs);

            if (count($dirs) > 1) {
                $this->recursiveCheckEmptyDirectory($dir);
            }
        }
    }


}
