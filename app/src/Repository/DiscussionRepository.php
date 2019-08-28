<?php
/**
 * Discussion repository.
 */

namespace App\Repository;

use App\Entity\Discussion;
use App\Entity\User;
use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class DiscussionRepository.
 *
 * @method Discussion|null find($id, $lockMode = null, $lockVersion = null)
 * @method Discussion|null findOneBy(array $criteria, array $orderBy = null)
 * @method Discussion[]    findAll()
 * @method Discussion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DiscussionRepository extends ServiceEntityRepository
{
    /**
     * DiscussionRepository constructor.
     *
     * @param \Symfony\Bridge\Doctrine\RegistryInterface $registry Registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Discussion::class);
    }

    /**
     * Query all records.
     *
     * @return QueryBuilder Query builder
     */
    public function queryAll(): QueryBuilder
    {
        return $this->getOrCreateQueryBuilder()
            ->orderBy('d.updatedAt', 'DESC');
    }
    /**
     * Query discussions by owner.
     *
     * @param User|null $user User Entity
     *
     * @return QueryBuilder Query builder
     */
    public function queryByOwner(User $user = null): QueryBuilder
    {
        $queryBuilder = $this->queryAll();

        if (!is_null($user)) {
            $queryBuilder->andWhere('d.author= :author')
                -> setParameter('author', $user);
        }

        return $queryBuilder;
    }
    /**
     * Query discussions by event.
     *
     * @param Event|null $event Event entity
     *
     * @return QueryBuilder Query builder
     */
    public function queryByEvent(Event $event = null) : QueryBuilder
    {
        $queryBuilder = $this->queryAll();

        if (!is_null($event)) {
            $queryBuilder->andWhere('d.event = :event')
                ->setParameter('event', $event);
        }

        return $queryBuilder;
    }
    /**
     *Query discussions by owner and event.
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
            $queryBuilder->andWhere('d.author= :author')
                -> setParameter('author', $user);
        }
        if (!is_null($event)) {
            $queryBuilder->andWhere('d.event= :event')
                -> setParameter('event', $event);
        }

        return $queryBuilder;
    }
    /**
     * Save record.
     *
     * @param Discussion $discussion Discussion entity
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save(Discussion $discussion): void
    {
        $this->_em->persist($discussion);
        $this->_em->flush($discussion);
    }

    /**
     * Delete record.
     *
     * @param Discussion $discussion Discussion entity
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function delete(Discussion $discussion): void
    {
        $this->_em->remove($discussion);
        $this->_em->flush($discussion);
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
        return $queryBuilder ?: $this->createQueryBuilder('d');
    }

    // /**
    //  * @return Discussion[] Returns an array of Discussion objects
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
    public function findOneBySomeField($value): ?Discussion
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
