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
        $maxDiscountCalculator = null;
        $maxDiscount = 0;

        /** @var DiscountCalculatorInterface $calculator */
        foreach ($this->discountCalculators as $calculator) {
            if ($calculator->supportsDiscountType($product)) {
                if (intval($calculator->getDiscountPercentage()) > $maxDiscount) {
                    $maxDiscount = intval($calculator->getDiscountPercentage());
                    $maxDiscountCalculator = $calculator;
                }
            }
        }

        return $maxDiscountCalculator;
    }
}
