<?php
/**
 * Interest repository.
 */

namespace App\Repository;

use App\Entity\Interest;
use App\Entity\User;
use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class InterestRepository.
 *
 * @method Interest|null find($id, $lockMode = null, $lockVersion = null)
 * @method Interest|null findOneBy(array $criteria, array $orderBy = null)
 * @method Interest[]    findAll()
 * @method Interest[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InterestRepository extends ServiceEntityRepository
{
    /**
     * InterestRepository constructor.
     *
     * @param \Symfony\Bridge\Doctrine\RegistryInterface $registry Registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Interest::class);
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
     * Query interests by owner.
     *
     * @param User|null $user User Entity
     *
     * @return QueryBuilder Query builder
     */
    public function queryByOwner(User $user = null): QueryBuilder
    {
        $queryBuilder = $this->queryAll();

        if (!is_null($user)) {
            $queryBuilder->andWhere('i.author= :author')
                -> setParameter('author', $user);
        }

        return $queryBuilder;
    }
    /**
     *Query interests by owner and event.
     *
     * @param User $user User Entity
     * @param Event|null $event
     *
     * @return QueryBuilder Query builder
     */
    public function queryByOwnerAndEvent(User $user = null, Event $event = null): QueryBuilder
    {
        $queryBuilder = $this->queryAll();

        if (!is_null($user)) {
            $queryBuilder->andWhere('i.author= :author')
                -> setParameter('author', $user);
        }
        if (!is_null($event)) {
            $queryBuilder->andWhere('i.event= :event')
                -> setParameter('event', $event);
        }

        return $queryBuilder;
    }
    /**
     * Save record.
     *
     * @param Interest $interest Interest entity
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save(Interest $interest): void
    {
        $this->_em->persist($interest);
        $this->_em->flush($interest);
    }

    /**
     * Delete record.
     *
     * @param Interest $interest Interest entity
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function delete(Interest $interest): void
    {
        $this->_em->remove($interest);
        $this->_em->flush($interest);
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
    //  * @return Interest[] Returns an array of Interest objects
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
    public function findOneBySomeField($value): ?Interest
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
