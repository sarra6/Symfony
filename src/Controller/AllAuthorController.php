<?php

namespace App\Controller;

use App\Entity\Author;
use App\Form\AuthorType;
use App\Repository\AuthorRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request ;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AllAuthorController extends AbstractController
{
    //        $authors = array( array('id' => 1,'username' =>
//            'Victor Hugo', 'email' =>
//            'victor.hugo@gmail.com ', 'nb_books' => 100), array('id' => 2,'username' => ' William Shakespeare', 'email' =>
//            ' william.shakespeare@gmail.com', 'nb_books' => 200 ), array('id' => 3, 'username' => 'Taha Hussein', 'email' =>
//            'taha.hussein@gmail.com', 'nb_books' => 300),
//        );

    #[Route('/all/author', name: 'app_all_author')]
    public function index(AuthorRepository $repository): Response
    {
        $authors = $repository ->findAll();
        return $this->render('author/index.html.twig', [
            'authors' => $authors
        ]);
    }

    #[Route('/add/author', name: 'add_author')]
    public function addBook(Request $request ,AuthorRepository $repository ,ManagerRegistry $managerRegistry ,ValidatorInterface $validator , ):Response
    {
        $author= new Author();
        $form = $this->createForm(AuthorType::class, $author);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $errors = $validator->validate($author);
            if (count($errors) > 0) {
                return new Response((string) $errors, 400);
            }
            $emailexist = $repository->findOneBy(['email' => $author->getEmail()]);
            if ($emailexist){
                return  new Response("email exist");
            }else{
                $em = $managerRegistry->getManager();
                $em->persist($author);
                $em->flush();
                return $this->redirectToRoute('app_all_author');
            }
        }
        return $this->render('author/add.html.twig', ['form' => $form->createView()]);
    }


    #[Route('/update/author/{id}', name: 'update_author')]
    public function update(Request $request,AuthorRepository $repository ,int $id ,  ManagerRegistry $managerRegistry): Response
    {
        $em= $managerRegistry->getManager();
        $author = $repository->find($id);
        if (!$author){
            return $this->render('author/error.html.twig');
        }
        $form = $this->createForm(AuthorType::class, $author);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirecttoRoute("app_all_author");
        }

        return $this->render('author/update.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    #[Route('/delete/author/{id}', name: 'delete_author')]
    public function delete($id,AuthorRepository $repository,ManagerRegistry $managerRegistry): Response
    {
        $author = $repository ->find($id);
        if ($author->getNbBooks() == 0) {
            $em = $managerRegistry->getManager();
            $em->remove($author);
            $em->flush();
            return $this->redirecttoRoute("app_all_author");
        }else {
            return  new Response("u can't delete this author");
        }
    }

    #[Route('/test/{id}', name: 'test')]
    public function test($id): Response
    {
        return $this->redirecttoRoute("app_all_author");
    }


}
