<?php

namespace App\Tests;

use App\Discount\Calculator\CategoryDiscountCalculator;
use App\Discount\DiscountTypeConfiguration;
use App\Entity\Discount;
use App\Entity\Product;
use App\Entity\ProductCategory;
use App\Repository\DiscountRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SKUDiscountCalculatorTest extends KernelTestCase
{
    public function testSupportsDiscountTypeReturnsRealTrue(): void
    {
        $kernel = self::bootKernel();
        /** EntityManager $entityManager */
        $entityManager = $kernel->getContainer()->get('doctrine')->getManager();
        $qb = $entityManager->createQueryBuilder();

        // $discountRepo = $entityManager->getRepository(DiscountRepository::class);
        $discountRepo = $this->createMock(DiscountRepository::class);
        $categoryDiscountCalc = new CategoryDiscountCalculator($discountRepo);

        $category = $qb->select('c')
            ->from(ProductCategory::class, 'c')
            ->where('c.name = :name')
            ->setParameter('name', 'sneakers')
            ->getQuery()
            ->getSingleResult();
        
        $qb = $entityManager->createQueryBuilder();
        $discount = $qb->select('d')
            ->from(Discount::class, 'd')
            ->where('d.discountType = :type')
            ->andWhere('d.productSKU = :sku')
            ->setParameter('type', DiscountTypeConfiguration::SKU_DISCOUNT_TYPE)
            ->setParameter('sku', '000003')
            ->getQuery()
            ->getSingleResult();

        $discountRepo->expects($this->any())
            ->method('findOneBy')
            ->willReturn($discount);

        // Product
        $product = new Product();
        $product->setName('Test Product');
        $product->setSKU('000003');
        $product->setProductCategory($category);

        $return = $categoryDiscountCalc->supportsDiscountType($product);
        $discountPercent = $categoryDiscountCalc->getDiscountPercentage();

        $this->assertSame('test', $kernel->getEnvironment());
        $this->assertTrue($return);
        $this->assertEquals($discountPercent, '15');
    }
}
