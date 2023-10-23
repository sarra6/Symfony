<?php

namespace App\Controller;

use App\Repository\AuthorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AuthorDetailsController extends AbstractController
{
    #[Route('/author_details/{id}', name: 'author_details')]
    public function index($id , AuthorRepository $repository): Response
    {
      $author = $repository->find($id);
        return $this->render('author_details/index.html.twig', [
            'author' => $author,
        ]);

    }
}
