<?php

namespace App\Repository;

use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Event>
 */
class EventRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Event::class);
    }

    public function persist(Event $event, bool $flush = false): void {
        $this->getEntityManager()->persist($event);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return array
     */
    public function getMeasurements(): array {
        $queryBuilder = $this->createQueryBuilder("event");
        $queryBuilder
            ->select("event.measurement")
            ->distinct();

        return $queryBuilder->getQuery()->getSingleColumnResult();
    }
}
