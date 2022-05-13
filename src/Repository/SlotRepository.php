<?php

namespace App\Repository;

use App\Entity\Slot;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Slot>
 *
 * @method Slot|null find($id, $lockMode = null, $lockVersion = null)
 * @method Slot|null findOneBy(array $criteria, array $orderBy = null)
 * @method Slot[]    findAll()
 * @method Slot[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SlotRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Slot::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Slot $entity, bool $flush = false): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Slot $entity, bool $flush = false): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function findNumberOfSlotsThisWeek(): array
    {
        $sql = 'select DAYNAME(begin_at) as day, COUNT(begin_at) as count from slot where begin_at > DATE_ADD(begin_at, INTERVAL(-WEEKDAY(begin_at)) DAY) GROUP BY DAYNAME(begin_at);';

        return $this->getEntityManager()->getConnection()->fetchAllAssociative($sql);
    }

    public function findTotalEarningsThisWeek(): array
    {
        $sql = 'select DAYNAME(begin_at) as day, SUM(price) as price from slot where begin_at > DATE_ADD(begin_at, INTERVAL(-WEEKDAY(begin_at)) DAY) GROUP BY DAYNAME(begin_at);';

        return $this->getEntityManager()->getConnection()->fetchAllAssociative($sql);
    }

    public function findNumberOfSlotsThisMonth(): array
    {
        $sql = 'select DAYOFMONTH(begin_at) as day, COUNT(begin_at) as count from slot where begin_at > DATE_ADD(begin_at, INTERVAL(-DAYOFMONTH(begin_at)) DAY) GROUP BY DAYOFMONTH(begin_at);';

        return $this->getEntityManager()->getConnection()->fetchAllAssociative($sql);
    }

    public function findTotalEarningsThisMonth(): array
    {
        $sql = 'select DAYOFMONTH(begin_at) as day, SUM(price) as price from slot where begin_at > DATE_ADD(begin_at, INTERVAL(-DAYOFMONTH(begin_at)) DAY) GROUP BY DAYOFMONTH(begin_at);';

        return $this->getEntityManager()->getConnection()->fetchAllAssociative($sql);
    }

    public function findTotalEarningsThisYear(): array
    {
        $sql = 'select MONTHNAME(begin_at) as month, SUM(price) as price from slot where begin_at > DATE_ADD(begin_at, INTERVAL(-MONTH(begin_at)) DAY) GROUP BY MONTHNAME(begin_at);';

        return $this->getEntityManager()->getConnection()->fetchAllAssociative($sql);
    }

//    /**
//     * @return Slot[] Returns an array of Slot objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Slot
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
