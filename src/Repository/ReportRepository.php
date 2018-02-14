<?php declare(strict_types=1);

namespace Loevgaard\DandomainConsignment\Repository;

use Doctrine\Common\Persistence\ManagerRegistry;
use Loevgaard\DandomainConsignment\Entity\Report;
use Loevgaard\DandomainConsignment\Repository\Generated\ReportRepositoryTrait;

class ReportRepository extends AbstractRepository
{
    use ReportRepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Report::class);
    }
}
