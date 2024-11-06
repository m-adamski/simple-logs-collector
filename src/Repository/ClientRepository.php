<?php

namespace App\Repository;

use App\Entity\Client;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @extends ServiceEntityRepository<Client>
 */
class ClientRepository extends ServiceEntityRepository implements UserLoaderInterface {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Client::class);
    }

    public function persist(Client $client, bool $flush = false): void {
        $this->getEntityManager()->persist($client);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Client $client, bool $flush = false): void {
        $this->getEntityManager()->remove($client);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function loadUserByIdentifier(string $identifier): ?UserInterface {
        $queryBuilder = $this->createQueryBuilder("client");
        $queryBuilder
            ->where($queryBuilder->expr()->eq("client.id", ":identifier"))
            ->andWhere($queryBuilder->expr()->eq("client.active", ":active"))
            ->setParameter("identifier", $identifier)
            ->setParameter("active", true);

        try {
            return $queryBuilder->getQuery()->getOneOrNullResult();
        } catch (NoResultException $e) {
            return null;
        }
    }
}
