<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Book>
 *
 * @method Book|null find($id, $lockMode = null, $lockVersion = null)
 * @method Book|null findOneBy(array $criteria, array $orderBy = null)
 * @method Book[]    findAll()
 * @method Book[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

//    /**
//     * @return Book[] Returns an array of Book objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('b.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Book
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
    public function searchBookByRef($id)
    {
       return $this->createQueryBuilder('b')
        ->where('b.id = :id')
        ->setParameter('id', $id)
        ->getQuery()
        ->getOneOrNullResult(); 
     }
    public function booksListByAuthors()
    {
        return $this->createQueryBuilder('b')
            ->leftJoin('b.author', 'a') 
            ->getQuery()
            ->getResult();
    }
    public function listBooksBefore2023() 
    {
        return $this->createQueryBuilder('b')
        ->join('b.author', 'author')
        ->Where('author.nb_Books > 10')
        ->andWhere('b.publicationDate < :date')
        ->groupBy('author.nb_Books')
        ->setParameter('date', new \DateTime('2023-01-01'))
        ->getQuery()
        ->getResult();
    }
    public function updateCategory()
    {
        return $this->createQueryBuilder('b')
            ->update(Book::class, 'b')
            ->set('b.category', ':newCategory')
            ->where('b.category = :oldCategory')
            ->setParameter('newCategory', 'Romance')
            ->setParameter('oldCategory', 'Science-Fiction');
    }
    public function nbRomance(){
        $em=$this->getEntityManager();
        $req=$em->createQuery("select count(b) from App\Entity\Book b where b.category=:c");
        $req->setParameter('c','Romance');
        $result=$req->getSingleScalarResult();
        return $result;
    }
    function findPublicationDate(){
        $em=$this->getEntityManager();
        $req=$em->createQuery("SELECT b from App\Entity\Book b WHERE b.publicationDate BETWEEN :startDate AND :endDate ");
        $req->setParameter('startDate','2014-01-01');
        $req->setParameter('endDate','2018-12-31');
        $result=$req->getResult();

        return $result;
    }
}
