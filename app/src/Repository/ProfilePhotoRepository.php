<?php
/**
 * ProfilePhoto repository.
 */

namespace App\Repository;

use App\Entity\ProfilePhoto;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class ProfilePhotoRepository.
 *
 * @method ProfilePhoto|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProfilePhoto|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProfilePhoto[]    findAll()
 * @method ProfilePhoto[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProfilePhotoRepository extends ServiceEntityRepository
{
    /**
     * ProfilePhotoRepository constructor.
     *
     * @param \Symfony\Bridge\Doctrine\RegistryInterface $registry Registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ProfilePhoto::class);
    }

    /**
     * Query all records.
     *
     * @return \Doctrine\ORM\QueryBuilder Query builder
     */
    public function queryAll(): QueryBuilder
    {
        return $this->getOrCreateQueryBuilder()
            ->orderBy('t.updatedAt', 'DESC');
    }

    /**
     * Save record.
     *
     * @param ProfilePhoto $profilePhoto ProfilePhoto entity
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save(ProfilePhoto $profilePhoto): void
    {
        $this->_em->persist($profilePhoto);
        $this->_em->flush($profilePhoto);
    }

    /**
     * Delete record.
     *
     * @param ProfilePhoto $profilePhoto ProfilePhoto entity
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function delete(ProfilePhoto $profilePhoto): void
    {
        $this->_em->remove($profilePhoto);
        $this->_em->flush($profilePhoto);
    }

    /**
     * Get or create new query builder.
     *
     * @param \Doctrine\ORM\QueryBuilder|null $queryBuilder Query builder
     *
     * @return \Doctrine\ORM\QueryBuilder Query builder
     */
    private function getOrCreateQueryBuilder(QueryBuilder $queryBuilder = null): QueryBuilder
    {
        return $queryBuilder ?: $this->createQueryBuilder('t');
    }

    // /**
    //  * @return ProfilePhoto[] Returns an array of ProfilePhoto objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ProfilePhoto
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}