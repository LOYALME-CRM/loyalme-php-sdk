<?php

use Codeception\Test\Unit;
use LoyalmeCRM\LoyalmePhpSdk\DeliveryMethod;
use LoyalmeCRM\LoyalmePhpSdk\Connection;

class DeliveryMethodTest extends Unit
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

    public function testCreatingDeliveryMethod()
    {
        $salt = rand(1000, 9999);
        $slug = 'DeliveryMethodSlug' . $salt;
        $titleEn = 'DeliveryMethodEn' . $salt;
        $titleRu = 'DeliveryMethodRu' . $salt;

        $deliveryMethodObject = new DeliveryMethod($this->_connection);
        $deliveryMethod = $deliveryMethodObject->get($slug, $titleEn, $titleRu);

        $this->assertTrue($deliveryMethodObject instanceof DeliveryMethod);
        $this->assertEquals($slug, $deliveryMethod->slug);
        $this->assertEquals($titleEn, $deliveryMethod->title_en);
        $this->assertEquals($titleRu, $deliveryMethod->title_ru);

        $titleEn = 'DeliveryMethodEn2' . $salt;
        $titleRu = 'DeliveryMethodRu2' . $salt;

        $deliveryMethodObject = new DeliveryMethod($this->_connection);
        $deliveryMethod = $deliveryMethodObject->get($slug, $titleEn, $titleRu);

        $this->assertTrue($deliveryMethodObject instanceof DeliveryMethod);
        $this->assertEquals($slug, $deliveryMethod->slug);
        $this->assertEquals($titleEn, $deliveryMethod->title_en);
        $this->assertEquals($titleRu, $deliveryMethod->title_ru);
    }
}
