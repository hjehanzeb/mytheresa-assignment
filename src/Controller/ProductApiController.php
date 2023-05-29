<?php

namespace App\Controller;

use App\Discount\Calculator\DiscountCalculatorCollector;
use App\Repository\ProductCategoryRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class ProductApiController extends AbstractController
{
    protected ProductCategoryRepository $productCategoryRepo;
    protected DiscountCalculatorCollector $calculatorCollector;
    public const CURRENCY = 'EUR';

    public function __construct(
        ProductCategoryRepository $productCategoryRepository,
        DiscountCalculatorCollector $discountCalculatorCollector)
    {
        $this->productCategoryRepo = $productCategoryRepository;
        $this->calculatorCollector = $discountCalculatorCollector;
    }

    #[Route('/products', name: 'mytheresa-app.products_api')]
    public function index(Request $request, ProductRepository $productRepository): JsonResponse
    {
        $categoryName = $request->query->get('category');
        $priceLessThan = $request->query->get('priceLessThan');
        $category = null;
        $response = [];

        if (!empty($categoryName)) {
            $category = $this->productCategoryRepo->findOneByName($categoryName);
        }

        $products = $productRepository->findByPriceLessThanCategory($priceLessThan, $category);

        foreach ($products as $product) {
            $calculator = $this->calculatorCollector->findOne($product);
            $finalPrice = $product->getPrice();
            $discountPercentage = null;

            if (!empty($calculator)) {
                $discountApplied = $calculator->calculateDiscount($product);
                $finalPrice = $product->getPrice() + $discountApplied;
                $discountPercentage = $calculator->getDiscountPercentage() . '%';
            }

            array_push($response, [
                'sku' => $product->getSKU(),
                'name' => $product->getName(),
                'category' => $product->getProductCategory()->getName(),
                'price' => [
                    'original' => $product->getPrice(),
                    'final' => $finalPrice,
                    'discount_percentage' => $discountPercentage,
                    'currency' => self::CURRENCY,
                ]
            ]);
        }

        return $this->json($response);
    }
}
