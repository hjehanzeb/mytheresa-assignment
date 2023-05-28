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
            $category = $this->productCategoryRepo->findByName($categoryName);
        }

        $products = $productRepository->findByPriceLessThanCategory($priceLessThan, $category);

        foreach ($products as $product) {
            $calculator = $this->calculatorCollector->findOne();
        }

        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/ProductApiController.php',
        ]);
    }
}
