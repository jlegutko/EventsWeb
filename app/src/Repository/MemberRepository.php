<?php
/**
 * Member repository.
 */

namespace App\Repository;

use App\Entity\Member;
use App\Entity\User;
use App\Entity\Group;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class MemberRepository.
 *
 * @method Member|null find($id, $lockMode = null, $lockVersion = null)
 * @method Member|null findOneBy(array $criteria, array $orderBy = null)
 * @method Member[]    findAll()
 * @method Member[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MemberRepository extends ServiceEntityRepository
{
    /**
     * MemberRepository constructor.
     *
     * @param \Symfony\Bridge\Doctrine\RegistryInterface $registry Registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Member::class);
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
     * Query members by owner.
     *
     * @param User|null $user User Entity
     *
     * @return QueryBuilder Query builder
     */
    public function queryByMember(User $user = null): QueryBuilder
    {
        $queryBuilder = $this->queryAll();

        if (!is_null($user)) {
            $queryBuilder->andWhere('i.member= :member')
                -> setParameter('member', $user);
        }

        return $queryBuilder;
    }
    /**
     * Query members by owner and group.
     *
     * @param User $user User Entity
     * @param Group|null $group
     *
     * @return QueryBuilder Query builder
     */
    public function queryByMemberAndGroup(User $user = null, Group $group = null): QueryBuilder
    {
        $queryBuilder = $this->queryAll();

        if (!is_null($user)) {
            $queryBuilder->andWhere('i.member= :member')
                -> setParameter('member', $user);
        }
        if (!is_null($group)) {
            $queryBuilder->andWhere('i.community= :community')
                -> setParameter('community', $group);
        }

        return $queryBuilder;
    }
    /**
     * Save record.
     *
     * @param Member $member Member entity
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save(Member $member): void
    {
        $this->_em->persist($member);
        $this->_em->flush($member);
    }

    /**
     * Delete record.
     *
     * @param Member $member Member entity
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function delete(Member $member): void
    {
        $this->_em->remove($member);
        $this->_em->flush($member);
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
    //  * @return Member[] Returns an array of Member objects
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
    public function findOneBySomeField($value): ?Member
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
