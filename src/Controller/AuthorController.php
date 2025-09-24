<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AuthorController extends AbstractController
{
    #[Route('/author', name: 'app_author')]




    public function index(): Response
    {
        return $this->render('author/index.html.twig', [
            'controller_name' => 'AuthorController',
        ]);
    }


    #[Route('/ShowAuthor/{name}', name: 'ShowAuthor')]
    public  function showAuthor($name)
    {
        return $this->render('author/show.html.twig', [
            'nom' => $name
 ]);}

 #[Route('/Afficher', name: 'Afficher')]
 
    public function Afficher()
    {
        return new Response( 'hello'  );
            
    
    }
#[Route('/listAuthor', name: 'listAuthor')]
    
public function list()
    {
         $authors = [
            ['id' => 1, 'picture' => 'assets/images/mm.png', 'username' => 'Victor Hugo', 'email' => 'victor.hugo@gmail.com', 'nb_books' => 100],
            ['id' => 2, 'picture' => 'assets/images/mo.png', 'username' => 'William Shakespeare', 'email' => 'william.shakespeare@gmail.com', 'nb_books' => 200],
            ['id' => 3, 'picture' => 'assets/images/m.png', 'username' => 'Taha Hussein', 'email' => 'taha.hussein@gmail.com', 'nb_books' => 300],
        ];

        return $this->render('author/list.html.twig', [
            'authors' => $authors]);
}
#[Route('/author/{id}', name: 'author_details')]
public function authorDetails($id)
{
    $authors = [
        ['id' => 1, 'picture' => 'assets/images/mm.png', 'username' => 'Victor Hugo', 'email' => 'victor.hugo@gmail.com', 'nb_books' => 100],
        ['id' => 2, 'picture' => 'assets/images/mo.png', 'username' => 'William Shakespeare', 'email' => 'william.shakespeare@gmail.com', 'nb_books' => 200],
        ['id' => 3, 'picture' => 'assets/images/m.png', 'username' => 'Taha Hussein', 'email' => 'taha.hussein@gmail.com', 'nb_books' => 300],
    ];

    // Chercher l’auteur par id
    $author = null;
    foreach ($authors as $a) {
        if ($a['id'] == $id) {
            $author = $a;
            break;
        }
    }

    if (!$author) {
        throw $this->createNotFoundException("Auteur introuvable !");
    }

    return $this->render('author/showAuthor.html.twig', [
        'author' => $author
    ]);
}





};


