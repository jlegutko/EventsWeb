<?php
/**
 * Grade repository.
 */

namespace App\Repository;

use App\Entity\Grade;
use App\Entity\Event;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class GradeRepository.
 *
 * @method Grade|null find($id, $lockMode = null, $lockVersion = null)
 * @method Grade|null findOneBy(array $criteria, array $orderBy = null)
 * @method Grade[]    findAll()
 * @method Grade[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GradeRepository extends ServiceEntityRepository
{
    /**
     * GradeRepository constructor.
     *
     * @param RegistryInterface $registry Registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Grade::class);
    }

    /**
     * Query all records.
     *
     * @return QueryBuilder Query builder
     */
    public function queryAll(): QueryBuilder
    {
        return $this->getOrCreateQueryBuilder()
            ->orderBy('g.updatedAt', 'DESC');
    }
    /**
     *Query interests by owner and event.
     *
     * @param User       $user  User Entity
     * @param Event|null $event
     *
     * @return QueryBuilder Query builder
     */
    public function queryByOwnerAndEvent(User $user = null, Event $event = null): QueryBuilder
    {
        $queryBuilder = $this->queryAll();

        if (!is_null($user)) {
            $queryBuilder->andWhere('g.user = :user')
                -> setParameter('user', $user);
        }
        if (!is_null($event)) {
            $queryBuilder->andWhere('g.event= :event')
                -> setParameter('event', $event);
        }

        return $this->getOrCreateQueryBuilder()
            ->orderBy('g.updatedAt', 'DESC');
    }
    /**
     * Query grades by event.
     *
     * @param Event|null $event Event entity
     *
     * @return QueryBuilder Query builder
     */
    public function queryByEvent(Event $event = null) : QueryBuilder
    {
        $queryBuilder = $this->queryAll();

        if (!is_null($event)) {
            $queryBuilder->andWhere('g.event = :event')
                ->setParameter('event', $event);
        }

        return $queryBuilder;
    }
    /**
     * Save record.
     *
     * @param Grade $grade Grade entity
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save(Grade $grade): void
    {
        $this->_em->persist($grade);
        $this->_em->flush($grade);
    }

    /**
     * Delete record.
     *
     * @param Grade $grade Grade entity
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function delete(Grade $grade): void
    {
        $this->_em->remove($grade);
        $this->_em->flush($grade);
    }

    /**
     * Get or create new query builder.
     *
     * @param QueryBuilder|null $queryBuilder Query builder
     *
     * @return QueryBuilder Query builder
     */
    private function getOrCreateQueryBuilder(QueryBuilder $queryBuilder = null): QueryBuilder
    {
        return $queryBuilder ?: $this->createQueryBuilder('g');
    }

    // /**
    //  * @return Grade[] Returns an array of Grade objects
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
    public function findOneBySomeField($value): ?Grade
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
