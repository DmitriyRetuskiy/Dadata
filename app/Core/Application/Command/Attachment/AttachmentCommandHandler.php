<?php

namespace App\Core\Application\Command\Attachment;

use App\Core\Application\Service\AttachmentService;
use App\Core\Infrastructure\Persistence\Repository\AttachmentRepository;
use App\Shared\Application\Command\AbstractCommandHandler;

class AttachmentCommandHandler extends AbstractCommandHandler
{
    /**
     * @param AttachmentRepository $attachmentRepository
     * @param AttachmentService $attachmentService

     */
    public function __construct(
        protected readonly AttachmentRepository $attachmentRepository = new AttachmentRepository(),
        protected readonly AttachmentService $attachmentService = new AttachmentService(),
    )
    {}

}
