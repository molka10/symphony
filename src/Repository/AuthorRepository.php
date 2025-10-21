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


    /**
     * Supprime tous les auteurs qui n'ont aucun livre
     */
    public function deleteAuthorsWithoutBooks(): int
    {
        $qb = $this->createQueryBuilder('a')
            ->delete()
            ->where('a.books IS EMPTY');

        return $qb->getQuery()->execute(); // retourne le nombre d'auteurs supprimés
    }



public function findAuthorsByBookCountRange(int $min, int $max)
{
    return $this->createQueryBuilder('a')
        ->leftJoin('a.books', 'b')
        ->groupBy('a.id')
        ->having('COUNT(b.id) BETWEEN :min AND :max')
        ->setParameter('min', $min)
        ->setParameter('max', $max)
        ->getQuery()
        ->getResult(); // ⚠️ pas getArrayResult()
}


}
