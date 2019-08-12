<?php
/**
 * Event repository.
 */

namespace App\Repository;

use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class EventRepository.
 *
 * @method Event|null find($id, $lockMode = null, $lockVersion = null)
 * @method Event|null findOneBy(array $criteria, array $orderBy = null)
 * @method Event[]    findAll()
 * @method Event[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventRepository extends ServiceEntityRepository
{
    /**
     * EventRepository constructor.
     *
     * @param \Symfony\Bridge\Doctrine\RegistryInterface $registry Registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Event::class);
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
     * @param Event $event Event entity
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save(Event $event): void
    {
        $this->_em->persist($event);
        $this->_em->flush($event);
    }

    /**
     * Delete record.
     *
     * @param Event $event Event entity
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function delete(Event $event): void
    {
        $this->_em->remove($event);
        $this->_em->flush($event);
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

    /**
     * Find events with string in name.
     *
     * @param string $search
     *
     * @return QueryBuilder Query builder
     */
    public function findByNamePart(string $search): QueryBuilder
    {
        return $this->getOrCreateQueryBuilder()
            ->where('t.name LIKE :search')
            ->setParameter('search', '%'.$search.'%')
            ->orderBy('LOCATE(:pos, t.name), t.name')
            ->setParameter('pos', $search)
            ->setMaxResults(30);
    }

    /**
     * Find events with string in date.
     *
     * @param string $search
     *
     * @return QueryBuilder Query builder
     */
    public function findByDatePart(string $search): QueryBuilder
    {
        return $this->getOrCreateQueryBuilder()
            ->where('t.eventDate LIKE :search')
            ->setParameter('search', '%'.$search.'%')
            ->orderBy('LOCATE(:pos, t.eventDate), t.eventDate')
            ->setParameter('pos', $search)
            ->setMaxResults(30);
    }

    /**
     * Find events with string in size.
     *
     * @param string $search
     *
     * @return QueryBuilder Query builder
     */
    public function findBySizePart(string $search): QueryBuilder
    {
        return $this->getOrCreateQueryBuilder()
            ->where('t.eventSize LIKE :search')
            ->setParameter('search', '%'.$search.'%')
            ->orderBy('LOCATE(:pos, t.eventSize), t.eventSize')
            ->setParameter('pos', $search)
            ->setMaxResults(30);
    }

    /**
     * Find events with string in price.
     *
     * @param string $search
     *
     * @return QueryBuilder Query builder
     */
    public function findByPricePart(string $search): QueryBuilder
    {
        return $this->getOrCreateQueryBuilder()
            ->where('t.price LIKE :search')
            ->setParameter('search', '%'.$search.'%')
            ->orderBy('LOCATE(:pos, t.price), t.price')
            ->setParameter('pos', $search)
            ->setMaxResults(30);
    }

    // /**
    //  * @return Event[] Returns an array of Event objects
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
    public function findOneBySomeField($value): ?Event
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