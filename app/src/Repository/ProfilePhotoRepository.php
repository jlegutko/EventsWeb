<?php

namespace App\Repository;

use App\Entity\Profilephoto;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Profilephoto|null find($id, $lockMode = null, $lockVersion = null)
 * @method Profilephoto|null findOneBy(array $criteria, array $orderBy = null)
 * @method Profilephoto[]    findAll()
 * @method Profilephoto[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProfilePhotoRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Profilephoto::class);
    }

    // /**
    //  * @return Profilephoto[] Returns an array of Profilephoto objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Profilephoto
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
