<?php

namespace App\Core\Infrastructure\Persistence\Repository;

use App\Core\Domain\Model\Attachment;
use App\Core\Domain\Repository\AttachmentRepositoryInterface;
use App\Shared\Infrastructure\Persistence\Doctrine\AbstractRepository;
use LaravelDoctrine\ORM\Facades\EntityManager;

class AttachmentRepository extends AbstractRepository implements AttachmentRepositoryInterface
{
    protected string $class = Attachment::class;

    public function findAllAttachment(
        ?string $ids = null,
        ?int $offset = null
    )
    : mixed
    {
        $limit = 48;
        $query = EntityManager::createQueryBuilder()
            ->select('u')
            ->from($this->class,'u')
            ->where("u.uuid = u.parent");

        if($ids!=null){
            $query = $query
                ->where("u.uuid IN (".$ids.")");
        }else{
            $query = $query
            ->orderBy('u.createdAt', 'DESC');
        }


        return $query
            //->orderBy('u.createdAt', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

    }

    public function countOfAttachments(): string|array|int
    {
        $query = EntityManager::createQueryBuilder()
            ->select('COUNT(u.uuid)')
            ->from($this->class,'u')
            ->where("u.uuid = u.parent");

        return $query->getQuery()->getScalarResult();
    }
}
