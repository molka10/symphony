<?php

namespace App\Controller;

use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class BookMaintenanceController extends AbstractController
{
    #[Route('/books/fix-categories', name: 'app_book_fix_categories', methods: ['POST'])]
    public function fixCategories(Request $request, BookRepository $bookRepo): Response
    {
        // Doit correspondre à {{ csrf_token('fix_categories') }} dans ton Twig
        if (!$this->isCsrfTokenValid('fix_categories', (string) $request->request->get('_token'))) {
            $this->addFlash('danger', 'Action non autorisée (CSRF).');
            return $this->redirectToRoute('app_book_list');
        }

        $count = $bookRepo->recategorizeSciFiToRomance();

        $this->addFlash('success', sprintf(
            '%d livre(s) recatégorisé(s) de "Science-Fiction" vers "Romance".',
            $count
        ));

        return $this->redirectToRoute('app_book_list');
    }

    // (Optionnel) Une route GET pour tester vite fait depuis le navigateur
    #[Route('/books/fix-categories/test', name: 'app_book_fix_categories_test', methods: ['GET'])]
    public function fixCategoriesTest(BookRepository $bookRepo): Response
    {
        $count = $bookRepo->recategorizeSciFiToRomance();
        return new Response("OK: $count livre(s) mis à jour.");
    }
}
