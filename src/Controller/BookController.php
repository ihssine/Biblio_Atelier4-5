<?php

namespace App\Controller;
use App\Repository\BookRepository;
use App\Entity\Book;
use App\Form\BookType;



use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;


class BookController extends AbstractController
{
    #[Route('/book', name: 'app_book')]
    public function index(): Response
    {
        return $this->render('book/index.html.twig', [
            'controller_name' => 'BookController',
        ]);
    }
    #[Route('/fetchb', name: 'fetchb')]
    public function fetchb(BookRepository $repo): Response
    {   
        $publishedBooks = $repo->findBy(['published' => true]);
        $result=$repo->findAll();
        $totalUnpublishedBooks = count($result) - count($publishedBooks);
        return $this-> render('book/list.html.twig', [
            'response'=>$publishedBooks,
            'totalPublishedBooks' => count($publishedBooks),
            'totalUnpublishedBooks' => $totalUnpublishedBooks, 
        ]);
    }

    #[Route('/addB', name: 'addB')]
    public function addB(BookRepository $repo , ManagerRegistry $mr , Request $req): Response
    {
        $b=new Book();
        $b->setPublished(true); 
        $form=$this->createForm(BookType::class,$b);
        $form->handleRequest($req);
        if($form->isSubmitted()){
        $em=$mr->getManager();
        $em->persist($b);
        $em->flush();
        $author = $b->getAuthor();
        $author->setNbBooks($author->getNbBooks() + 1);

        $em->persist($author);
        $em->flush();
        return $this->redirectToRoute('fetchb');
        }
        return $this->render('book/add.html.twig',['f'=>$form->createView()]);
    }
    #[Route('/updateB/{id}', name: 'updateB')]
    public function updateB($id, BookRepository $repo, ManagerRegistry $mr, Request $req): Response
    {
        $b = $repo->find($id);
        $form = $this->createForm(BookType::class, $b);
        $form->handleRequest($req);

    if ($form->isSubmitted()) {
        $em = $mr->getManager();
        $em->flush();
        return $this->redirectToRoute('fetchb');
    }

    return $this->render('book/update.html.twig', ['f' => $form->createView()]);
    }
    #[Route('/removeB/{id}', name: 'removeB')]
    public function deleteB(BookRepository $repo , $id ,ManagerRegistry $mr): Response
    {
        $b=$repo->find($id);
        $em=$mr->getManager();
        $em->remove($b);
        $em->flush();
        $author = $b->getAuthor();
        if ($author->getNbBooks() === 0) {
        $em->remove($author);
        $em->flush();
        }
        return $this->redirectToRoute('fetchb');
    }
    #[Route('/showB/{id}', name: 'showB')]
   public function showBook($id, BookRepository $repo): Response
    {
    $result = $repo->find($id);

    return $this->render('book/show.html.twig', [
        'book' => $result,
    ]);
    }
    #[Route('/searchbook', name: 'searchbook')]
    public function searchBooks(Request $request, BookRepository $repo): Response
    {
    if ($request->isMethod('POST')) {
        $id = $request->request->get('id');
        $result = $repo->searchBookByRef($id);
    } 
        $publishedBooks = $repo->findBy(['published' => true]);
        $result=$repo->findAll();
        $totalUnpublishedBooks = count($result) - count($publishedBooks);
        return $this-> render('book/list.html.twig', [
            'response'=>$publishedBooks,
            'totalPublishedBooks' => count($publishedBooks),
            'totalUnpublishedBooks' => $totalUnpublishedBooks, 
        ]);
    
   }

   #[Route('/ListByAuthors', name: 'ListByAuthors')]
   public function booksListByAuthors(BookRepository $repo)
    {
    $repo = $repo->booksListByAuthors();
    return $this->render('book/list1.html.twig', [
        'response' => $repo, 
    ]);
    }
    #[Route('/book2023', name: 'book2023')]
    public function BooksBefore2023(BookRepository $repo): Response
    {
        $result = $repo->listBooksBefore2023();

        return $this->render('book/list2023.html.twig', [
            'response' => $result,
        ]);
    }
    #[Route('/updateCategory', name: 'updateCategory')]
    public function updateCategory(BookRepository $repo, EntityManagerInterface $em): Response
    {
        $result = $repo->updateCategory($em);

        return $this->redirectToRoute('fetchb');
        
    }
    #[Route('/nbRomance', name: 'nbRomance')]
    public function nbRomance(BookRepository $repo): Response
    {
        $nb = $repo->nbRomance();

        return $this->render('book/nbromance.html.twig', [
            'result' => $nb,]);
        
    }
    #[Route('/findPubDate', name: 'findPubDate')]
    public function findPublicationDate(BookRepository $repo): Response
    {
        $book = $repo->findPublicationDate();

        return $this->render('book/Published.html.twig', [
            'response' => $book,]);
        
    }
}
