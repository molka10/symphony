<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Book>
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    // -------------------- 1️⃣ Nombre de livres Romance --------------------
    public function countRomanceBooks(): int
    {
        return (int) $this->createQueryBuilder('b')
            ->select('COUNT(b.id)')
            ->where('b.category = :cat')
            ->setParameter('cat', 'Romance')
            ->getQuery()
            ->getSingleScalarResult();
    }

    // -------------------- 2️⃣ Livres publiés entre deux dates --------------------
    public function findBooksBetweenDates(\DateTimeInterface $start, \DateTimeInterface $end): array
    {
        return $this->createQueryBuilder('b')
            ->where('b.publicationDate BETWEEN :start AND :end')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->orderBy('b.publicationDate', 'ASC')
            ->getQuery()
            ->getResult();
    }

    // -------------------- 3️⃣ Livres publiés (enabled = true) --------------------
    public function findPublishedBooks(): array
    {
        return $this->createQueryBuilder('b')
            ->where('b.enabled = true')
            ->orderBy('b.publicationDate', 'DESC')
            ->getQuery()
            ->getResult();
    }

    // -------------------- 4️⃣ Supprimer les livres non publiés --------------------
    public function deleteUnpublishedBooks(): int
    {
        $qb = $this->_em->createQueryBuilder()
            ->delete(Book::class, 'b')
            ->where('b.enabled = false');

        return $qb->getQuery()->execute();
    }
}
