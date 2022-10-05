<?php

namespace App\Repository;

use App\Entity\Order;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Order>
 *
 * @method Order|null find($id, $lockMode = null, $lockVersion = null)
 * @method Order|null findOneBy(array $criteria, array $orderBy = null)
 * @method Order[]    findAll()
 * @method Order[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    public function add(Order $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Order $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findForPagination(array $filters):Query
    {
        $status = $filters['status'] ?? null;
        $totalFrom = $filters['from'] ?? null;
        $totalTo = $filters['to'] ?? null;
        $createdAtFrom = $filters['createdAtFrom'] ?? null;
        $createdAtTo = $filters['createdAtTo'] ?? null;
        $user = $filters['user'] ?? null;

        $qb = $this->createQueryBuilder('o');

        if ($status) {
            $qb->andWhere('o.status = :status')
                ->setParameter('status', $status);
        }

        if ($totalFrom !== null) {
            $qb->andWhere('o.total >=  :from')
                ->setParameter('from', $totalFrom);
        }

        if ($totalTo !== null) {
            $qb->andWhere('o.total <=  :to')
                ->setParameter('to', $totalTo);
        }

        if ($createdAtFrom !== null) {
            $qb->andWhere('o.createdAt >=  :createdAtFrom')
                ->setParameter('createdAtFrom', $createdAtFrom);
        }

        if ($createdAtTo !== null) {
            $qb->andWhere('o.createdAt <=  :createdAtTo')
                ->setParameter('createdAtTo', $createdAtTo);
        }
        if($user){
            $qb->andWhere('o.user = :user')
                ->setParameter('user', $user);
        }

        return $qb
            ->orderBy('o.createdAt', 'DESC')
            ->getQuery();

    }



//    /**
//     * @return Order[] Returns an array of Order objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('o.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Order
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
