<?php

namespace App\Repository;

use App\Entity\Product;
use App\Entity\ProductCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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

    public function save(Product $entity, bool $flush = false): void
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

    /**
     * @return Product[] Returns an array of Product objects
     */
    public function findByPriceLessThanCategory(?float $price, ?ProductCategory $category): array
    {
        $qb = $this->createQueryBuilder('p');

        if (!empty($price)) {
            $qb ->andWhere('p.price <= :price')
                ->setParameter('price', $price);
        }

        if (!empty($category)) {
            $qb ->andWhere('p.productCategory = :categoryId')
                ->setParameter('categoryId', $category->getId());
        }

        return $qb->orderBy('p.id', 'ASC')
           ->setMaxResults(5)
           ->getQuery()
           ->getResult();
   }
}
