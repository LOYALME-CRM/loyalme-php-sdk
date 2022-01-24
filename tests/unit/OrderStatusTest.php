<?php

use Codeception\Test\Unit;
use LoyalmeCRM\LoyalmePhpSdk\OrderStatus;
use LoyalmeCRM\LoyalmePhpSdk\Connection;

class OrderStatusTest extends Unit
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

    public function testCreatingOrderStatus()
    {
        $salt = rand(1000, 9999);
        $slug = 'OrderStatusSlug' . $salt;
        $titleEn = 'OrderStatusEn' . $salt;
        $titleRu = 'OrderStatusRu' . $salt;

        $orderStatusObject = new OrderStatus($this->_connection);
        $orderStatus = $orderStatusObject->get($slug, $titleEn, $titleRu);

        $this->assertTrue($orderStatusObject instanceof OrderStatus);
        $this->assertEquals($slug, $orderStatus->slug);
        $this->assertEquals($titleEn, $orderStatus->title_en);
        $this->assertEquals($titleRu, $orderStatus->title_ru);

        $titleEn = 'OrderStatusEn2' . $salt;
        $titleRu = 'OrderStatusRu2' . $salt;

        $orderStatusObject = new OrderStatus($this->_connection);
        $orderStatus = $orderStatusObject->get($slug, $titleEn, $titleRu);

        $this->assertTrue($orderStatusObject instanceof OrderStatus);
        $this->assertEquals($slug, $orderStatus->slug);
        $this->assertEquals($titleEn, $orderStatus->title_en);
        $this->assertEquals($titleRu, $orderStatus->title_ru);
    }
}
