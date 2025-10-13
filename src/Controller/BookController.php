<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Author;
use App\Form\BookType;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/book')]
class BookController extends AbstractController
{
    #[Route('/', name: 'app_book_index')]
    public function index(BookRepository $bookRepository): Response
    {
        // Afficher uniquement les livres publiés
        $books = $bookRepository->findBy(['published' => true]);

        // Compter publiés et non publiés
        $nbPublished = count($bookRepository->findBy(['published' => true]));
        $nbUnpublished = count($bookRepository->findBy(['published' => false]));

        return $this->render('book/index.html.twig', [
            'books' => $books,
            'nbPublished' => $nbPublished,
            'nbUnpublished' => $nbUnpublished,
        ]);
    }

    #[Route('/new', name: 'app_book_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $book = new Book();
        $book->setPublished(true); // Par défaut publié

        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Incrémenter le nombre de livres de l'auteur choisi
            $author = $book->getAuthor();
            if ($author) {
                $author->setNbBooks(($author->getNbBooks() ?? 0) + 1);
            }

            $em->persist($book);
            $em->flush();

            return $this->redirectToRoute('app_book_index');
        }

        return $this->render('book/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_book_show')]
    public function show(Book $book): Response
    {
        return $this->render('book/show.html.twig', [
            'book' => $book,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_book_edit')]
    public function edit(Request $request, Book $book, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('app_book_index');
        }

        return $this->render('book/edit.html.twig', [
            'form' => $form,
            'book' => $book,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_book_delete')]
    public function delete(Book $book, EntityManagerInterface $em): Response
    {
        $em->remove($book);
        $em->flush();

        return $this->redirectToRoute('app_book_index');
    }
}
