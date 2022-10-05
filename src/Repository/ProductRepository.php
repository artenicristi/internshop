<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 *
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function add(Product $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Product $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findForPagination(array $filters): Query
    {
        $name = $filters['name'] ?? null;
        $priceFrom = $filters['from'] ?? null;
        $priceTo = $filters['to'] ?? null;
        $code = $filters['code'] ?? null;
        $category = $filters['category'] ?? null;

        $qb = $this->createQueryBuilder('p');

        if ($name) {
            $name = '%'.$name.'%';
            $qb->andWhere('p.name LIKE :name')
                ->setParameter('name', $name);
        }

        if (null !== $priceFrom) {
            $qb->andWhere('p.price >=  :from')
                ->setParameter('from', $priceFrom);
        }

        if (null !== $priceTo) {
            $qb->andWhere('p.price <=  :to')
                ->setParameter('to', $priceTo);
        }

        if ($code) {
            $code = '%'.$code.'%';
            $qb->andWhere('p.code LIKE :code')
                ->setParameter('code', $code);
        }

        if ($category) {
            $category = $this->getEntityManager()->getRepository(Category::class)->findOneBy(['name' => $category]);

            if (!$category) {
                $qb->andWhere('1 = 0');
            } else {
                $qb->andWhere('p.category = :category')
                    ->setParameter('category', $category->getId());
            }
        }

        return $qb
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery();
    }

//    /**
//     * @return Product[] Returns an array of Product objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Product
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
