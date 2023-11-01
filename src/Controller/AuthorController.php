<?php

namespace App\Controller;
use App\Repository\AuthorRepository;
use App\Entity\Author;
use App\Form\AuthorType;



use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;


class AuthorController extends AbstractController
{
    #[Route('/author', name: 'app_author')]
    public function index(): Response
    {
        return $this->render('author/index.html.twig', [
            'controller_name' => 'AuthorController',
        ]);
    }
    #[Route('/fetch', name: 'fetch')]
    public function fetch(AuthorRepository $repo): Response
    {
        $result=$repo->findAll();
        return $this-> render('author/liste.html.twig', [
            'response'=>$result,
        ]);
    }
    #[Route('/add1', name: 'add1')]
    public function add(AuthorRepository $repo , ManagerRegistry $mr ): Response
    {
        $a=new Author();
        $a->setUsername('author4');
        $a->setEmail('author4@gmail.com');
        $em=$mr->getManager();
        $em->persist($a);
        $em->flush();
        return $this->redirectToRoute('fetch');
    }
    #[Route('/add2', name: 'add2')]
    public function add2(AuthorRepository $repo , ManagerRegistry $mr , Request $req): Response
    {
        $a=new Author();
        $form=$this->createForm(AuthorType::class,$a);
        $form->handleRequest($req);
        if($form->isSubmitted()){
        $em=$mr->getManager();
        $em->persist($a);
        $em->flush();
        return $this->redirectToRoute('fetch');
        }
        return $this->render('author/add.html.twig',['f'=>$form->createView()]);
    }
    #[Route('/remove/{id}', name: 'remove')]
    public function delete(AuthorRepository $repo , $id ,ManagerRegistry $mr): Response
    {
        $a=$repo->find($id);
        $em=$mr->getManager();
        $em->remove($a);
        $em->flush();
        return $this->redirectToRoute('fetch');
    }
    #[Route('/update/{id}', name: 'update')]
    public function update($id, AuthorRepository $repo, ManagerRegistry $mr, Request $req): Response
    {
        $a = $repo->find($id);
        $form = $this->createForm(AuthorType::class, $a);
        $form->handleRequest($req);

    if ($form->isSubmitted()) {
        $em = $mr->getManager();
        $em->flush();
        return $this->redirectToRoute('fetch');
    }

    return $this->render('author/update.html.twig', ['f' => $form->createView()]);
    }
    #[Route('/minmax', name: 'minmax')]
    public function listAuthorsByBookCount(Request $req, AuthorRepository $repo)
    {
    $form = $this->createForm(AuthorType::class);
    $form->handleRequest($req);
    $authors = [];
    if ($form->isSubmitted() && $form->isValid()) {
        $data = $form->getData();
        $min = $data['min'];
        $max = $data['max'];
        $authors = $repo->minmax($min, $max);
    }

    return $this->render('author/minmax.html.twig', ['response' => $authors, 'f' => $form->createView()]);
    }
    #[Route('/Delete', name:'Delete')]
    public function Deletebook(AuthorRepository $repo)
    {
            $repo->Delete0books();
            return $this->redirectToRoute('fetch');
    }
}
