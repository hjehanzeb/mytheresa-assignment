<?php

namespace App\Tests;

use App\Discount\Calculator\CategoryDiscountCalculator;
use App\Discount\DiscountTypeConfiguration;
use App\Entity\Discount;
use App\Entity\Product;
use App\Entity\ProductCategory;
use App\Repository\DiscountRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CategoryDiscountCalculatorTest extends KernelTestCase
{

    public function testSupportsDiscountTypeReturnsTrue(): void
    {
        $kernel = self::bootKernel();

        $discount = new Discount();
        $discount->setDiscountPercent('30');
        $discount->setDiscountType(DiscountTypeConfiguration::SKU_DISCOUNT_TYPE);
        $discount->setProductSKU('00001');

        // /** EntityManager $entityManager */
        // $entityManager = $kernel->getContainer()->get('doctrine.orm.entity_manager');
        $discountRepo = $this->createMock(DiscountRepository::class);
        $discountRepo->expects($this->any())
            ->method('findOneBy')
            ->willReturn($discount);

        $categoryDiscountCalc = new CategoryDiscountCalculator($discountRepo);

        // Create Product
        $product = new Product();
        $product->setName('Test Product');
        $product->setSKU('00001');

        $return = $categoryDiscountCalc->supportsDiscountType($product);

        $this->assertSame('test', $kernel->getEnvironment());
        $this->assertTrue($return);
    }

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
            ->setParameter('name', 'boots')
            ->getQuery()
            ->getSingleResult();
        
        $qb = $entityManager->createQueryBuilder();
        $discount = $qb->select('d')
            ->from(Discount::class, 'd')
            ->where('d.discountType = :type')
            ->andWhere('d.productCategory = :category')
            ->setParameter('type', DiscountTypeConfiguration::CATEGORY_DISCOUNT_TYPE)
            ->setParameter('category', $category)
            ->getQuery()
            ->getSingleResult();

        $discountRepo->expects($this->any())
            ->method('findOneBy')
            ->willReturn($discount);

        // Product
        $product = new Product();
        $product->setName('Test Product');
        $product->setSKU('00002');
        $product->setProductCategory($category);

        $return = $categoryDiscountCalc->supportsDiscountType($product);
        $discountPercent = $categoryDiscountCalc->getDiscountPercentage();

        $this->assertSame('test', $kernel->getEnvironment());
        $this->assertTrue($return);
        $this->assertEquals($discountPercent, '30');
    }

    public function testCalculateDiscount(): void
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
            ->setParameter('name', 'boots')
            ->getQuery()
            ->getSingleResult();
        
        $discount = new Discount();
        $discount->setDiscountPercent('10');
        $discount->setDiscountType(DiscountTypeConfiguration::SKU_DISCOUNT_TYPE);
        $discount->setProductSKU('00001');

        $discountRepo->expects($this->any())
            ->method('findOneBy')
            ->willReturn($discount);

        // Product
        $product = new Product();
        $product->setName('Test Product');
        $product->setSKU('00002');
        $product->setProductCategory($category);
        $product->setPrice('5000');

        $categoryDiscountCalc->supportsDiscountType($product);
        $discountedPrice = $categoryDiscountCalc->calculateDiscount($product);

        $this->assertEquals($discountedPrice, -500);
    }
}
