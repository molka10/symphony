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





    // src/Repository/BookRepository.php
public function searchBookByRef(string $ref): ?Book
{
    return $this->createQueryBuilder('b')
        ->andWhere('b.ref = :ref')
        ->setParameter('ref', $ref)
        ->getQuery()
        ->getOneOrNullResult();
}


public function booksListByAuthors(): array
{
    return $this->createQueryBuilder('b')
        ->leftJoin('b.author', 'a')->addSelect('a')
        ->orderBy('a.username', 'ASC')
        ->addOrderBy('b.title', 'ASC')
        ->getQuery()
        ->getResult();
}





public function publishedBefore2023WithProlificAuthors(): array
{
    return $this->createQueryBuilder('b')
        ->leftJoin('b.author', 'a')->addSelect('a')
        ->andWhere('b.published = :pub')->setParameter('pub', true)
        ->andWhere('b.publishedAt < :cutoff')->setParameter('cutoff', new \DateTime('2023-01-01'))
        ->andWhere('a.nbBooks > :min')->setParameter('min', 10)
        ->orderBy('b.publishedAt', 'DESC')
        ->getQuery()
        ->getResult();
}





public function recategorizeSciFiToRomance(): int
{
    return $this->getEntityManager()
        ->createQueryBuilder()
        ->update(Book::class, 'b')
        ->set('b.category', ':new')
        ->where('b.category = :old')
        ->setParameter('new', 'Romance')
        ->setParameter('old', 'Science-Fiction')
        ->getQuery()
        ->execute();
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
