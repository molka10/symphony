<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookType;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/books')]
final class BookController extends AbstractController
{
    // -------------------- Liste des livres --------------------
    #[Route('', name: 'app_book_list')]
    public function index(BookRepository $bookRepo): Response
    {
        $books = $bookRepo->findBy([], ['publicationDate' => 'DESC']);

        // Exemple : nombre de livres Romance
        $romanceCount = $bookRepo->countRomanceBooks();

        return $this->render('book/index.html.twig', [
            'books' => $books,
            'romanceCount' => $romanceCount,
        ]);
    }

    // -------------------- Nouveau livre --------------------
    #[Route('/new', name: 'app_book_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $book = new Book();
        $book->setEnabled(true);

        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($book);
            $em->flush();

            $this->addFlash('success', 'Livre ajouté ✅');
            return $this->redirectToRoute('app_book_list');
        }

        return $this->render('book/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // -------------------- Modifier un livre --------------------
    #[Route('/{id}/edit', name: 'app_book_edit')]
    public function edit(int $id, Request $request, EntityManagerInterface $em, BookRepository $bookRepo): Response
    {
        $book = $bookRepo->find($id);
        if (!$book) {
            throw $this->createNotFoundException('Livre non trouvé.');
        }

        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Livre modifié ✅');
            return $this->redirectToRoute('app_book_list');
        }

        return $this->render('book/edit.html.twig', [
            'form' => $form->createView(),
            'book' => $book,
        ]);
    }

    // -------------------- Supprimer un livre --------------------
    #[Route('/{id}/delete', name: 'app_book_delete', methods: ['POST'])]
    public function delete(int $id, Request $request, EntityManagerInterface $em, BookRepository $bookRepo): Response
    {
        $book = $bookRepo->find($id);
        if (!$book) {
            throw $this->createNotFoundException('Livre non trouvé.');
        }

        if (!$this->isCsrfTokenValid('delete'.$book->getId(), $request->request->get('_token'))) {
            throw $this->createAccessDeniedException();
        }

        $em->remove($book);
        $em->flush();

        $this->addFlash('danger', 'Livre supprimé ❌');
        return $this->redirectToRoute('app_book_list');
    }

    // -------------------- Détails d’un livre --------------------
    #[Route('/{id<\d+>}', name: 'app_book_show')]
    public function show(int $id, BookRepository $bookRepo): Response
    {
        $book = $bookRepo->find($id);
        if (!$book) {
            throw $this->createNotFoundException('Livre non trouvé.');
        }

        return $this->render('book/show.html.twig', [
            'book' => $book,
        ]);
    }

    // -------------------- Supprimer tous les livres non publiés --------------------
    #[Route('/delete-unpublished', name: 'app_book_delete_unpublished')]
    public function deleteUnpublishedBooks(EntityManagerInterface $em, BookRepository $bookRepo): Response
    {
        $books = $bookRepo->findBy(['enabled' => false]);

        foreach ($books as $book) {
            $em->remove($book);
        }

        $em->flush();

        $this->addFlash('success', count($books) . ' livre(s) non publié(s) ont été supprimé(s).');
        return $this->redirectToRoute('app_book_list');
    }

    // -------------------- Livres entre deux dates --------------------
    #[Route('/between-dates', name: 'app_book_between_dates')]
    public function booksBetweenDates(BookRepository $bookRepo): Response
    {
        $start = new \DateTime('2014-01-01');
        $end = new \DateTime('2018-12-31');

        $books = $bookRepo->findBooksBetweenDates($start, $end);

        return $this->render('book/between_dates.html.twig', [
            'books' => $books,
            'start' => $start,
            'end' => $end,
        ]);
    }
}
