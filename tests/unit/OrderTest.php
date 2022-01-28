<?php

use Codeception\Test\Unit;
use LoyalmeCRM\LoyalmePhpSdk\Order;
use LoyalmeCRM\LoyalmePhpSdk\Client;
use LoyalmeCRM\LoyalmePhpSdk\Product;
use LoyalmeCRM\LoyalmePhpSdk\Connection;

class OrderTest extends Unit
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

    public function testCreatingOrder()
    {
        $externalId = rand(10000, 99999);

        $clientRest = new Client($this->_connection);
        $client = $clientRest->get(
            $externalId,
            '',
            'Mike',
            'Mikeln',
            'Mikemn'
        );
        $this->assertTrue(is_numeric($client->id));

        $orderAmount = 0;
        $products = [];
        $countOfItemsInOrder = rand(1, 10);
        for ($i = 0; $i < $countOfItemsInOrder; $i++) {
            $productObject = new Product($this->_connection);
            $extItemId = rand(10000, 99999);
            $barcode = md5(date('Ymdhms') . rand(10000, 99999));
            $title = 'productTestSDK-' . rand(1000, 9999);
            $price = rand(1000, 9999);
            $typeId = Product::PRODUCT_TYPE_PRODUCT;

            $productQuantity = rand(1, 5);
            $productObject = new Product($this->_connection);
            $product = $productObject->get(
                $extItemId,
                $barcode,
                $title,
                $price,
                null,
                1,
                $typeId
            );
            $products[$product->id] = $product;
            $products[$product->id]->quantity = $productQuantity;
            $orderAmount += ($price * $productQuantity);
        }

        $orderStatus = null;
        $paymentMethod = null;
        $deliveryMethod = null;
        $paymentStatus = null;

        $externalId = rand(10000, 99999);
        $orderObject = new Order($this->_connection);
        $order1 = $orderObject->get($externalId, $client, $products, null, null, $orderStatus, $paymentMethod, $deliveryMethod, $paymentStatus);

        $this->assertTrue($order1 instanceof Order);
        $this->assertEquals(count($order1->products), count($products));
        $this->assertEquals($orderAmount, $order1->amount);
        $this->assertEquals($externalId, $order1->ext_order_id);
        $this->assertTrue($this->_compareOrderList($products, $order1->products));

        $orderAmount = 0;
        $products = [];
        $countOfItemsInOrder = rand(1, 10);
        for ($i = 0; $i < $countOfItemsInOrder; $i++) {
            $productObject = new Product($this->_connection);
            $extItemId = rand(10000, 99999);
            $barcode = md5(date('Ymdhms') . rand(10000, 99999));
            $title = 'productTestSDK-' . rand(1000, 9999);
            $price = rand(1000, 9999);
            $typeId = Product::PRODUCT_TYPE_PRODUCT;

            $productQuantity = rand(1, 5);
            $productObject = new Product($this->_connection);
            $product = $productObject->get(
                $extItemId,
                $barcode,
                $title,
                $price,
                null,
                1,
                $typeId
            );
            $products[$product->id] = $product;
            $products[$product->id]->quantity = $productQuantity;
            $orderAmount += ($price * $productQuantity);
        }

        $orderObject = new Order($this->_connection);
        $order2 = $orderObject->get($externalId, $client, $products);

        $this->assertTrue($order2 instanceof Order);
        $this->assertEquals($order2->id, $order1->id);
        $this->assertEquals(count($order2->products), count($products));
        $this->assertEquals($orderAmount, $order2->amount);
        $this->assertEquals($externalId, $order2->ext_order_id);
        $this->assertTrue($this->_compareOrderList($products, $order2->products));
    }

    /**
     * @param array $originalOrderList
     * @param array $finalOrderList
     * @return bool
     */
    private function _compareOrderList(array $originalOrderList, array $finalOrderList): bool
    {
        $result = true;

        foreach ($finalOrderList as $product) {
            if (isset($originalOrderList[$product['product_id']])) {
                $originalProduct = $originalOrderList[$product['product_id']];
                $result = $originalProduct->id == $product['product_id'] && $originalProduct->quantity == $product['quantity'] && $originalProduct->price == $product['price'];
                if ($result == false) {
                    break;
                }
            } else {
                $result = false;
                break;
            }
        }

        return $result;
    }
}
