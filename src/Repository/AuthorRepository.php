<?php

namespace App\Repository;

use App\Entity\Author;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Author>
 *
 * @method Author|null find($id, $lockMode = null, $lockVersion = null)
 * @method Author|null findOneBy(array $criteria, array $orderBy = null)
 * @method Author[]    findAll()
 * @method Author[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AuthorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Author::class);
    }

//    /**
//     * @return Author[] Returns an array of Author objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Author
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
public function listAuthorByEmail(){
    $em=$this->getEntityManager();
    return $em->createQueryBuilder('a')
        ->orderBy('a.email','ASC')
        ->getQuery()
        ->getResult();
}
function minmax($min,$max){
    $em=$this->getEntityManager();
    $req=$em->createQuery("SELECT a from App\Entity\Author a WHERE a.nb_Books BETWEEN :min AND :max");
    $req->setParameter('min',$min);
    $req->setParameter('max',$max);
    $result=$req->getResult();
    return $result;    
}
function Delete0books(){
    $em=$this->getEntityManager();
    $req=$em->createQuery("DELETE App\Entity\Author a WHERE a.nbr_books = 0");
    $result=$req->getResult();
    return $result;
}
}
