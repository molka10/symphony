<?php

namespace App\Controller;
use App\Entity\Author;
use App\Form\AuthorType;
use App\Repository\AuthorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\AuthorSearchType;


class AuthorController extends AbstractController
{
 #[Route('/authors', name: 'app_author_list')]
public function index(Request $request, AuthorRepository $authorRepo): Response
{
    // Récupérer les valeurs min et max depuis l'URL
    $min = $request->query->getInt('minBooks', 0);         // valeur par défaut 0
    $max = $request->query->getInt('maxBooks', PHP_INT_MAX); // valeur par défaut maximum

    $authors = $authorRepo->findAuthorsByBookCountRange($min, $max);

    return $this->render('author/index.html.twig', [
        'authors' => $authors,
        'minBooks' => $min,
        'maxBooks' => $max,
    ]);
}


    






#[Route('/authors/new', name: 'app_author_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $author = new Author();
        $form = $this->createForm(AuthorType::class, $author);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($author);
            $em->flush();
            $this->addFlash('success', 'Auteur ajouté ');
            return $this->redirectToRoute('app_author_list');
        }

        return $this->render('author/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/authors/{id}', name: 'app_author_show', requirements: ['id' => '\d+'])]
    public function show(AuthorRepository $repo, int $id): Response
    {
        $author = $repo->find($id);
        if (!$author) {
            throw $this->createNotFoundException('Auteur non trouvé');
        }

        return $this->render('author/show.html.twig', [
            'author' => $author,
        ]);
    }
    #[Route('/authors/{id}/edit', name: 'app_author_edit')]
    public function edit(Request $request, EntityManagerInterface $em, Author $author): Response
    {
        $form = $this->createForm(AuthorType::class, $author);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Auteur modifié avec succès ');
            return $this->redirectToRoute('app_author_list');
        }

        return $this->render('author/edit.html.twig', [
            'form' => $form->createView(),
            'author' => $author,
        ]);
    }











    #[Route('/authors/search', name: 'app_author_search')]
public function search(Request $request, AuthorRepository $authorRepo): Response
{
    $form = $this->createForm(AuthorSearchType::class);
    $form->handleRequest($request);

    $authors = [];

    if ($form->isSubmitted() && $form->isValid()) {
        $data = $form->getData();
        $min = $data['minBooks'] ?? null;
        $max = $data['maxBooks'] ?? null;

        $authors = $authorRepo->findAuthorsByBookCount($min, $max);
    }

    return $this->render('author/search.html.twig', [
        'form' => $form->createView(),
        'authors' => $authors,
    ]);
}



    #[Route('/authors/delete-empty', name: 'app_author_delete_empty')]
public function deleteEmptyAuthors(AuthorRepository $authorRepo): Response
{
    $count = $authorRepo->deleteAuthorsWithoutBooks();

    $this->addFlash('success', "$count auteur(s) sans livre ont été supprimé(s).");

    return $this->redirectToRoute('app_author_list'); // route vers la liste des auteurs
}


#[Route('/authors/{id}/delete', name: 'app_author_delete', methods: ['POST'])]
public function delete(Request $request, EntityManagerInterface $em, Author $author): Response
{
    // 🔹 Vérification temporaire des données envoyées
    dump($request->request->all()); // affiche le contenu de la requête (doit contenir _token)
    // die; // décommente si tu veux bloquer pour voir le dump

    // 🔹 Sécurité CSRF
    if (!$this->isCsrfTokenValid('delete'.$author->getId(), $request->request->get('_token'))) {
        throw $this->createAccessDeniedException('Jeton CSRF invalide');
    }

    // 🔹 Suppression de l'auteur
    $em->remove($author);
    $em->flush();

    // 🔹 Message flash et redirection
    $this->addFlash('danger', 'Auteur supprimé');
    return $this->redirectToRoute('app_author_list');
}}

