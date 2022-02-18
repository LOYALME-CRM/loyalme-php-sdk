<?php

use Faker\Factory;
use Codeception\Test\Unit;
use LoyalmeCRM\LoyalmePhpSdk\Client;
use LoyalmeCRM\LoyalmePhpSdk\Product;
use LoyalmeCRM\LoyalmePhpSdk\Connection;
use LoyalmeCRM\LoyalmePhpSdk\ProductList;
use LoyalmeCRM\LoyalmePhpSdk\Interfaces\ClientInterface;
use LoyalmeCRM\LoyalmePhpSdk\Interfaces\ProductInterface;
use LoyalmeCRM\LoyalmePhpSdk\Exceptions\ProductListException;
use LoyalmeCRM\LoyalmePhpSdk\Interfaces\ProductListInterface;

class ProductListTest extends Unit
{
    /**
     * @var UnitTester
     */
    protected $tester;

    /**
     * @var Connection
     */
    protected $_connection;

    protected function _before()
    {
        $token = $this->tester->getConfig('token');
        $apiUrl = $this->tester->getConfig('apiUrl');
        $brandId = $this->tester->getConfig('brandId');
        $pointId = $this->tester->getConfig('pointId');
        $personId = $this->tester->getConfig('personId');
        $this->_connection = new Connection($token, $apiUrl, $brandId, $pointId, $personId);
    }

    public function testCreatingObject()
    {
        $this->assertTrue($this->_connection instanceof Connection);
    }

    /**
     * @dataProvider systemNameProvider
     */
    public function testCreatingAndUpdatingProductList(string $systemName, string $name)
    {
        try {
            $productListObject = new ProductList($this->_connection);
            $productList = $productListObject->get($systemName, $name);
            $this->assertTrue($productList instanceof ProductList);
            $this->assertEquals($systemName, $productList->system_name);
            $this->assertEquals($name, $productList->name);
        } catch (ProductListException $exception) {
            if ((int) $exception->getCode() === 422) {
                $this->assertTrue(in_array(
                    $exception->getMessage(),
                    ['Could not update ProductList', 'Could not create new ProductList']
                ));
            } else {
                $this->fail($exception->getMessage());
            }
        }
        // Trying to update Product list by same system_name
        try {
            $productListObject = new ProductList($this->_connection);
            $productList = $productListObject->get($systemName, $name);
            $this->assertTrue($productList instanceof ProductList);
            $this->assertEquals($systemName, $productList->system_name);
            $this->assertEquals($name, $productList->name);
        } catch (ProductListException $exception) {
            if ((int) $exception->getCode() === 422) {
                $this->assertTrue(in_array(
                    $exception->getMessage(),
                    ['Could not update ProductList', 'Could not create new ProductList']
                ));
            } else {
                $this->fail($exception->getMessage());
            }
        }
    }

    public function testTrowingProductListException()
    {
        $this->expectException(ProductListException::class);
        $this->expectExceptionMessage('Parameter [systemName] is required.');
        $productListObject = new ProductList($this->_connection);
        $productListObject->get();
    }

    /**
     * @return Generator
     */
    public function systemNameProvider(): Generator
    {
        $time = time();
        $faker = Factory::create();
        for ($i = 0; $i < rand(1, 10);  $i++) {
            yield [$faker->firstName . $time, $faker->lastName . $time];
        }
    }

    private function _startTestUpdatingContentOfProductList(ProductListInterface $productList, ClientInterface $client = null, ProductInterface $relatedProduct = null)
    {
        $extItemId = rand(100000, 999999);
        $barcode = md5(date('Ymdhms') . rand(10000, 99999));
        $title = 'productTestSDK-' . rand(1000, 9999);
        $price = rand(1000, 9999);
        $productObject = new Product($this->_connection);
        $product1 = $productObject->get(
            $extItemId,
            $barcode,
            $title,
            $price
        );
        $this->assertTrue($product1 instanceof Product);

        $extItemId = rand(100000, 999999);
        $barcode = md5(date('Ymdhms') . rand(10000, 99999));
        $title = 'productTestSDK-' . rand(1000, 9999);
        $price = rand(1000, 9999);
        $productObject2 = new Product($this->_connection);
        $product2 = $productObject2->get(
            $extItemId,
            $barcode,
            $title,
            $price
        );
        $this->assertTrue($product2 instanceof Product);

        $products = [$product1->id => $product1, $product2->id => $product2];
        $content = $productList->updateContent($productList, $products, $client, $relatedProduct);
        $this->_checkEqualsOfContent($content, $products, $client, $relatedProduct);

        $extItemId = rand(100000, 999999);
        $barcode = md5(date('Ymdhms') . rand(10000, 99999));
        $title = 'productTestSDK-' . rand(1000, 9999);
        $price = rand(1000, 9999);
        $productObject3 = new Product($this->_connection);
        $product3 = $productObject3->get(
            $extItemId,
            $barcode,
            $title,
            $price
        );
        $this->assertTrue($product2 instanceof Product);
        $product1->quantity = 5;
        $products[$product1->id] = $product1;
        $product2->price = 500;
        $products[$product2->id] = $product2;
        $products[$product3->id] = $product3;

        $content = $productList->updateContent($productList, $products, $client, $relatedProduct);
        $this->_checkEqualsOfContent($content, $products, $client, $relatedProduct);
    }

    /**
     * @param array $content
     * @param array $products
     * @return void
     */
    private function _checkEqualsOfContent(array $content, array $products, ClientInterface $client = null, ProductInterface $relatedProduct = null): void
    {
        $this->assertEquals(count($products), count($content));

        foreach ($content as $productFromContent) {
            $product = $products[$productFromContent['product_id']];
            $this->assertEquals($productFromContent['client_id'], $client->id ?? null);
            $this->assertEquals($productFromContent['related_product_id'], $relatedProduct->id ?? null);
            $this->assertEquals($productFromContent['price_per_item'], $product->price);
            $this->assertEquals($productFromContent['quantity'], $product->quantity ?? 1);
        }
    }

    private function _creatingTestData()
    {
        $time = time();
        $faker = Factory::create();

        $productListObject = new ProductList($this->_connection);
        $productList = $productListObject->get($faker->firstName . $time, $faker->lastName . $time);

        $clientRest = new Client($this->_connection);
        $externalId = rand(100000, 999999);
        $client = $clientRest->get(
            $externalId,
            null,
            $faker->firstName
        );
        $this->assertTrue($client instanceof Client);

        $externalId = rand(100000, 999999);
        $price = rand(1000, 9999);
        $productObject = new Product($this->_connection);
        $relatedProduct = $productObject->get(
            $externalId,
            null,
            $faker->title,
            $price
        );
        $this->assertTrue($relatedProduct instanceof Product);

        return [
            'productList' => $productList,
            'client' => $client,
            'relatedProduct' => $relatedProduct,
        ];
    }

    /**
     * @param ProductListInterface $productList
     * @param ClientInterface|null $client
     * @param ProductInterface|null $relatedProduct
     * @return void
     */
    public function _startTestClearingContentOfProductList(ProductListInterface $productList, ?ClientInterface $client = null, ?ProductInterface $relatedProduct = null): void
    {
        $extItemId = rand(100000, 999999);
        $barcode = md5(date('Ymdhms') . rand(10000, 99999));
        $title = 'productTestSDK-' . rand(1000, 9999);
        $price = rand(1000, 9999);
        $productObject = new Product($this->_connection);
        $product1 = $productObject->get(
            $extItemId,
            $barcode,
            $title,
            $price
        );
        $this->assertTrue($product1 instanceof Product);

        $extItemId = rand(100000, 999999);
        $barcode = md5(date('Ymdhms') . rand(10000, 99999));
        $title = 'productTestSDK-' . rand(1000, 9999);
        $price = rand(1000, 9999);
        $productObject2 = new Product($this->_connection);
        $product2 = $productObject2->get(
            $extItemId,
            $barcode,
            $title,
            $price
        );
        $this->assertTrue($product2 instanceof Product);

        $products = [$product1->id => $product1, $product2->id => $product2];
        $content = $productList->updateContent($productList, $products, $client, $relatedProduct);
        $this->_checkEqualsOfContent($content, $products, $client, $relatedProduct);

        $this->assertTrue($productList->clear($productList, $client, $relatedProduct));
    }

    public function testUpdatingContentOfProductList()
    {
        $res = $this->_creatingTestData();
        $productListTests = [
            [$res['productList']],
            [$res['productList'], $res['client']],
            [$res['productList'], $res['client'], $res['relatedProduct']],
            [$res['productList'], null, $res['relatedProduct']],
        ];

        foreach ($productListTests as $productListTest) {
            $this->_startTestUpdatingContentOfProductList($productListTest[0], $productListTest[1] ?? null, $productListTest[2] ?? null);
        }
    }

    public function testClearingOfProductList()
    {
        $res = $this->_creatingTestData();
        $productListTests = [
            [$res['productList']],
            [$res['productList'], $res['client']],
            [$res['productList'], $res['client'], $res['relatedProduct']],
            [$res['productList'], null, $res['relatedProduct']],
        ];

        foreach ($productListTests as $productListTest) {
            $this->_startTestClearingContentOfProductList($productListTest[0], $productListTest[1] ?? null, $productListTest[2] ?? null);
        }
    }
}
