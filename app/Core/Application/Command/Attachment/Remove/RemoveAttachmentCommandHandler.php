<?php

namespace App\Core\Application\Command\Attachment\Remove;

use App\Core\Application\Command\Attachment\AttachmentCommandHandler;
use Infrastructure\Utils\Exception\UnprocessableException;


class RemoveAttachmentCommandHandler extends AttachmentCommandHandler
{
    /**
     * @throws UnprocessableException
     */
    public function __invoke(
        ?RemoveAttachmentCommand $command
    ): bool
    {
        foreach ($command->ids as $item) {
            $attachment = $this->attachmentRepository->find($item->uuid);
            if ($attachment == null) throw new UnprocessableException('Файл с UUID = '.$item->uuid.' не существует.', 404);

            $this->attachmentService->deleteFileFromStorage($attachment);
            $this->attachmentRepository->remove($attachment);
        }

        return true;
    }

}
