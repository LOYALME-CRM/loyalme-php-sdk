<?php

use Codeception\Test\Unit;
use LoyalmeCRM\LoyalmePhpSdk\Connection;
use LoyalmeCRM\LoyalmePhpSdk\Exceptions\ProductListException;
use LoyalmeCRM\LoyalmePhpSdk\ProductList;

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
        $faker = Faker\Factory::create();
        for ($i = 0; $i < 10;  $i++) {
            yield [$faker->firstName, $faker->lastName];
        }
    }
}
