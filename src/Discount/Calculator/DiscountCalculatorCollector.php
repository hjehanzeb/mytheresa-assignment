<?php

namespace App\Discount\Calculator;

use App\Entity\Discount;
use App\Entity\Product;

class DiscountCalculatorCollector
{
    private iterable $discountCalculators;

    public function __construct(iterable $discountCalculators)
    {
        $this->discountCalculators = $discountCalculators;
    }

    public function findOne(Product $product): ?DiscountCalculatorInterface
    {
        /** @var DiscountCalculatorInterface $calculator */
        foreach ($this->discountCalculators as $calculator) {
            if ($calculator->supportsDiscountType($product)) {
                return $calculator;
            }
        }

        return null;
    }
}
