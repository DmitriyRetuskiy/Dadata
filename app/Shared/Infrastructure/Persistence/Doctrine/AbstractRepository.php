<?php

namespace App\Shared\Infrastructure\Persistence\Doctrine;

use App\Shared\Domain\Model\EntityInterface;
use LaravelDoctrine\ORM\Facades\EntityManager;
use App\Shared\Domain\Model\AggregateInterface;

use Doctrine\ORM\Query\ResultSetMapping;

abstract class AbstractRepository
{
    /**
     * @return static
     */
    public static function make(): static
    {
        return new static();
    }

    /**
     * @param mixed $uuid
     *
     * @return AggregateInterface|EntityInterface
     */
    public function find(
        mixed $uuid
    ): ?object
    {
        return EntityManager::find(
            $this->class,
            $uuid
        );
    }

    public function findAll(
        ?string $type = null
    )
    : mixed
    {
        $query = EntityManager::createQueryBuilder()
            ->select('u')
            ->from($this->class,'u');

        if($type!=null){
            $query = $query
                ->where("u.type =:type")
                ->setParameter('type', $type);
        }

        return $query->orderBy('u.name', 'ASC')
            ->getQuery()
            ->getResult();
    }


    /**
     * @param mixed $uuid
     *
     * @return mixed
     */
    public function findBySlug(
        mixed $slug,
        mixed $uuid
    ): mixed
    {
        return EntityManager::createQuery(
            "select u from ".$this->class." as u where u.slug=:slug and u.uuid!=:uuid"
        )
            ->setParameter("slug", $slug)
            ->setParameter("uuid", $uuid)
            ->getArrayResult();
    }

    /**
     * @param AggregateInterface|EntityInterface $entity
     *
     * @return void
     */
    public function add(
        AggregateInterface|EntityInterface $entity
    ): void
    {
        EntityManager::persist($entity);
        EntityManager::flush();
    }

    /**
     * @param AggregateInterface|EntityInterface $entity
     *
     * @return void
     */
    public function remove(
        AggregateInterface|EntityInterface $entity
    ): void
    {
        EntityManager::remove($entity);
        EntityManager::flush();
    }
}
