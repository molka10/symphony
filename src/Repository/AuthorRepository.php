<?php

namespace App\Repository;

use App\Entity\Author;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class AuthorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Author::class);
    }

    public function listAuthorByEmail(): array
    {
        return $this->createQueryBuilder('a')
            ->orderBy('a.email', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /** Supprime tous les auteurs qui n'ont aucun livre */
    public function deleteAuthorsWithoutBooks(): int
    {
        return $this->createQueryBuilder('a')
            ->delete()
            ->where('a.books IS EMPTY')
            ->getQuery()
            ->execute();
    }

    /** ✅ Ta méthode utilisée dans le contrôleur /authors (bornes incluses) */
    public function findAuthorsByBookCountRange(int $min, int $max): array
    {
        return $this->createQueryBuilder('a')
            ->leftJoin('a.books', 'b')
            ->groupBy('a.id')
            ->having('COUNT(b.id) BETWEEN :min AND :max')
            ->setParameter('min', $min)
            ->setParameter('max', $max)
            ->orderBy('COUNT(b.id)', 'ASC')
            ->addOrderBy('a.username', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /** (Optionnel) Variante avec min/max optionnels pour ton action /authors/search */
    public function findAuthorsByBookCount(?int $min, ?int $max): array
    {
        $qb = $this->createQueryBuilder('a')
            ->leftJoin('a.books', 'b')
            ->groupBy('a.id');

        if ($min !== null) {
            $qb->having('COUNT(b.id) >= :min')->setParameter('min', $min);
        }
        if ($max !== null) {
            $qb->andHaving('COUNT(b.id) <= :max')->setParameter('max', $max);
        }

        return $qb->orderBy('COUNT(b.id)', 'ASC')
                  ->addOrderBy('a.username', 'ASC')
                  ->getQuery()
                  ->getResult();
    }
}
