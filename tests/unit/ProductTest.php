<?php

use Codeception\Test\Unit;
use LoyalmeCRM\LoyalmePhpSdk\Product;
use LoyalmeCRM\LoyalmePhpSdk\Category;
use LoyalmeCRM\LoyalmePhpSdk\Connection;

class ProductTest extends Unit
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

    protected function _after()
    {
        
    }

    public function testConnectionObject()
    {
        $this->assertTrue($this->_connection instanceof Connection);
    }

    public function testCreatingProduct()
    {
        $extId = md5(date('Ymdhms') . rand(1000, 9999));
        $nameOfCategory = 'category' . $extId;
        $categoryObject = new Category($this->_connection);
        $category = $categoryObject->get($extId, $nameOfCategory);

        $extId2 = md5(date('Ymdhms') . rand(1000, 9999));
        $nameOfCategory2 = 'category' . $extId;
        $categoryObject2 = new Category($this->_connection);
        $category2 = $categoryObject2->get($extId2, $nameOfCategory2);

        $this->assertTrue($category instanceof Category);
        $this->assertEquals($extId, $category->external_id);

        $extItemId = rand(1000, 9999);
        $barcode = md5(date('Ymdhms') . rand(10000, 99999));
        $title = 'productTestSDK-' . rand(1000, 9999);
        $price = rand(1000, 9999);
        $typeId = Product::PRODUCT_TYPE_PRODUCT;
        $categories = [$category, $category2];

        $productObject = new Product($this->_connection);
        $product = $productObject->get(
            $extItemId,
            $barcode,
            $title,
            $price,
            null,
            1,
            $typeId,
            1,
            $categories
        );

        $this->assertTrue($product instanceof Product);
        $this->assertEquals($extItemId, $product->ext_item_id);
        $this->assertEquals($barcode, $product->barcode);
        $this->assertEquals($title, $product->title);
        $this->assertEquals($price, $product->price);
        $this->assertEquals($typeId, $product->type_id);
        $this->assertEquals(count($categories), count($product->categories));

        $title2 = 'productTestSDK-2' . rand(1000, 9999);
        $categories2 = [$category2];
        $productObject2 = new Product($this->_connection);
        $product2 = $productObject2->get(
            $extItemId,
            null,
            $title2,
            null,
            null,
            1,
            1,
            1,
            $categories2
        );

        $this->assertTrue($product2 instanceof Product);
        $this->assertEquals($product->id, $product2->id);
        $this->assertEquals($extItemId, $product2->ext_item_id);
        $this->assertEquals($product->barcode, $product2->barcode);
        $this->assertEquals($title2, $product2->title);
        $this->assertEquals($product->price, $product2->price);
        $this->assertEquals(count($categories2), count($product2->categories));
        $this->assertEquals($product->type_id, $product2->type_id);

        $title3 = 'productTestSDK-3' . rand(1000, 9999);
        $categories3 = [];
        $productObject3 = new Product($this->_connection);
        $product3 = $productObject3->get(
            null,
            $barcode,
            $title3,
            null,
            null,
            1,
            1,
            1,
            $categories3
        );

        $this->assertTrue($product3 instanceof Product);
        $this->assertEquals($product->id, $product3->id);
        $this->assertEquals($extItemId, $product3->ext_item_id);
        $this->assertEquals($product->barcode, $product3->barcode);
        $this->assertEquals($title3, $product3->title);
        $this->assertEquals($product->price, $product3->price);
        $this->assertEquals(count($categories3), count($product3->categories));
        $this->assertEquals($product->type_id, $product3->type_id);
    }
}
