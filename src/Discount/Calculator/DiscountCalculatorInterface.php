<?php

namespace App\Discount\Calculator;

use App\Entity\Product;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.discount_calculator')]
interface DiscountCalculatorInterface
{
    /**
     * @param string $type
     * @return bool
     */
    public function supportsDiscountType(Product $product): bool;

    /**
     * @param Product $product
     * @return int|null
     */
    public function calculateDiscount(Product $product): ?int;
}
