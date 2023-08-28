<?php

namespace App\Core\Application\Command\Attachment\Create;

use App\Core\Application\Command\Attachment\AttachmentCommandHandler;
use Infrastructure\Utils\Exception\NotFoundException;
use phpDocumentor\Reflection\File;

class CreateAttachmentCommandHandler extends AttachmentCommandHandler
{
    /**
     * @throws UnprocessableException
     * @throws NotFoundException
     */
    public function __invoke(
        CreateAttachmentCommand $command
    ): array // string проверка на путь к файлу
    {
        $ids = [];

        //не злись что закоменчено прояви доблесть, все работает
//        foreach ($command->userFile as $userFile) {
//            // $userFile = C:\OpenServer\userdata\temp\upload\phpC3C5.tmp
//            $attachment = $this->attachmentService->uploadFileByType($userFile);
////            $this->attachmentRepository->add($attachment);
//            $ids[] = $attachment->uuid()->uuid();
//        }

        $typeCommand = gettype($command->userFile);
        var_dump($typeCommand);
        $attachment = $this->attachmentService->uploadFileByType($command->userFile);
        $ids[0] = $attachment;
        $this->attachmentRepository->add($attachment);


        return $ids;
    }

}
