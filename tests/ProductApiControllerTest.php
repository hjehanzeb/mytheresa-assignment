<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProductApiControllerTest extends WebTestCase
{
    public function testApiWorking(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/products');

        $this->assertResponseIsSuccessful();
    }

    public function testApiResponse(): void
    {
        $client = static::createClient();
        $client->request('GET', '/products');
        $response = $client->getResponse()->getContent();

        $responseArray = json_decode($response, true);

        $this->assertCount(5, $responseArray);
        $this->assertArrayHasKey('sku', $responseArray[0]);
        $this->assertArrayHasKey('name', $responseArray[0]);
        $this->assertArrayHasKey('category', $responseArray[0]);
        $this->assertArrayHasKey('price', $responseArray[0]);
        $this->assertEquals($responseArray[0]['price']['final'], 62300);
        $this->assertEquals($responseArray[0]['price']['discount_percentage'], '30%');
        $this->assertEquals($responseArray[1]['price']['final'], 69300);
        $this->assertEquals($responseArray[1]['price']['discount_percentage'], '30%');
        $this->assertEquals($responseArray[2]['price']['final'], 49700);
        $this->assertEquals($responseArray[2]['price']['discount_percentage'], '30%');
        $this->assertResponseIsSuccessful();
    }

    public function testApiWithCategoryFilter(): void
    {
        $client = static::createClient();
        $client->request('GET', '/products?category=sneakers');
        $response = $client->getResponse()->getContent();

        $responseArray = json_decode($response, true);

        $this->assertCount(1, $responseArray);
        $this->assertEquals($responseArray[0]['price']['final'], 59000);
        $this->assertEquals($responseArray[0]['sku'], '000005');
        $this->assertNull($responseArray[0]['price']['discount_percentage']);
        $this->assertResponseIsSuccessful();
    }

    public function testApiWithPriceFilterReturningNoRecords(): void
    {
        $client = static::createClient();
        $client->request('GET', '/products?priceLessThan=10000');
        $response = $client->getResponse()->getContent();

        $responseArray = json_decode($response, true);

        $this->assertCount(0, $responseArray);
    }
    
    public function testApiWithPriceFilterReturning2Records(): void
    {
        $client = static::createClient();
        $client->request('GET', '/products?priceLessThan=71000');
        $response = $client->getResponse()->getContent();

        $responseArray = json_decode($response, true);

        $this->assertCount(2, $responseArray);
        $this->assertEquals($responseArray[0]['price']['final'], 49700);
        $this->assertEquals($responseArray[0]['sku'], '000003');
        $this->assertEquals($responseArray[1]['price']['final'], 59000);
        $this->assertEquals($responseArray[1]['sku'], '000005');
    }
}
