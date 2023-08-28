<?php

namespace App\Core\Application\Query;

use App\Core\Infrastructure\Persistence\Repository\AttachmentRepository;
use App\Shared\Application\Query\AbstractQueryHandler;
use Doctrine\ORM\AbstractQuery;

class AttachmentQueryHandler extends AbstractQueryHandler
{
    /**
     * @param AttachmentRepository $attachmentRepository
     */
    #[Pure] public function __construct(
        protected readonly AttachmentRepository $attachmentRepository = new AttachmentRepository()
    )
    {}

}
