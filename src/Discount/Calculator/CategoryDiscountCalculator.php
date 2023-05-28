<?php

namespace App\Discount\Calculator;

use App\Discount\Calculator\DiscountCalculatorInterface;
use App\Discount\DiscountTypeConfiguration;
use App\Entity\Discount;
use App\Entity\Product;
use App\Repository\DiscountRepository;

class CategoryDiscountCalculator implements DiscountCalculatorInterface
{
    private DiscountRepository $discountRepo;
    protected ?Discount $discount;

    public function __construct(DiscountRepository $discountRepository)
    {
        $this->discountRepo = $discountRepository;
        $this->discount = null;
    }
    
    public function supportsDiscountType(Product $product): bool
    {
        $discount = $this->discountRepo->findOneBy([
            'discountType' => DiscountTypeConfiguration::CATEGORY_DISCOUNT_TYPE,
            'productCategory' => $product->getProductCategory(),
        ]);

        $this->discount = $discount;

        if (empty($discount)) {
            return false;
        }

        return true;
    }

    public function calculateDiscount(Product $product): ?int
    {
        // $discount = $this->discountRepo->findOneBy([
        //     'discountType' => DiscountTypeConfiguration::CATEGORY_DISCOUNT_TYPE,
        //     'productCategory' => $product->getProductCategory(),
        // ]);

        // if (empty($discount)) {
        //     return null;
        // }

        return -1 * $product->getPrice() * $this->discount->getDiscountPercent();
    }
}
