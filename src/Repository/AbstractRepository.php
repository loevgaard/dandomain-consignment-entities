<?php declare(strict_types=1);

namespace Loevgaard\DandomainConsignment\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Loevgaard\DandomainConsignment\Repository\Generated\AbstractRepositoryTrait;

abstract class AbstractRepository extends ServiceEntityRepository
{
    use AbstractRepositoryTrait;

    public function __construct(ManagerRegistry $registry, string $entityClass)
    {
        parent::__construct($registry, $entityClass);
    }

    /**
     * @param $object
     * @throws \Doctrine\ORM\ORMException
     */
    public function persist($object) : void
    {
        $this->getEntityManager()->persist($object);
    }

    /**
     * @param null|object|array $entity
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function flush($entity = null) : void
    {
        $this->getEntityManager()->flush($entity);
    }

    /**
     * Helper method for calling persist and flush successively
     *
     * @param object $entity
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save($entity)
    {
        $this->persist($entity);
        $this->flush();
    }
}
