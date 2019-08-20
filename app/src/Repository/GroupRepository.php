<?php
/**
 * Group repository.
 */

namespace App\Repository;

use App\Entity\Group;
use App\Entity\User;
use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class GroupRepository.
 *
 * @method Group|null find($id, $lockMode = null, $lockVersion = null)
 * @method Group|null findOneBy(array $criteria, array $orderBy = null)
 * @method Group[]    findAll()
 * @method Group[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GroupRepository extends ServiceEntityRepository
{
    /**
     * GroupRepository constructor.
     *
     * @param \Symfony\Bridge\Doctrine\RegistryInterface $registry Registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Group::class);
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
     * Query groups by owner.
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
     *Query groups by owner and event.
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
     * @param Group $group Group entity
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save(Group $group): void
    {
        $this->_em->persist($group);
        $this->_em->flush($group);
    }

    /**
     * Delete record.
     *
     * @param Group $group Group entity
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function delete(Group $group): void
    {
        $this->_em->remove($group);
        $this->_em->flush($group);
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
    //  * @return Group[] Returns an array of Group objects
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
    public function findOneBySomeField($value): ?Group
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
