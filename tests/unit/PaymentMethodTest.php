<?php

use Codeception\Test\Unit;
use LoyalmeCRM\LoyalmePhpSdk\PaymentMethod;
use LoyalmeCRM\LoyalmePhpSdk\Connection;

class PaymentMethodTest extends Unit
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

    public function testCreatingPaymentMethod()
    {
        $salt = rand(1000, 9999);
        $slug = 'PaymentMethodSlug' . $salt;
        $titleEn = 'PaymentMethodEn' . $salt;
        $titleRu = 'PaymentMethodRu' . $salt;

        $paymentMethodObject = new PaymentMethod($this->_connection);
        $paymentMethod = $paymentMethodObject->get($slug, $titleEn, $titleRu);

        $this->assertTrue($paymentMethodObject instanceof PaymentMethod);
        $this->assertEquals($slug, $paymentMethod->slug);
        $this->assertEquals($titleEn, $paymentMethod->title_en);
        $this->assertEquals($titleRu, $paymentMethod->title_ru);

        $titleEn = 'PaymentMethodEn2' . $salt;
        $titleRu = 'PaymentMethodRu2' . $salt;

        $paymentMethodObject = new PaymentMethod($this->_connection);
        $paymentMethod = $paymentMethodObject->get($slug, $titleEn, $titleRu);

        $this->assertTrue($paymentMethodObject instanceof PaymentMethod);
        $this->assertEquals($slug, $paymentMethod->slug);
        $this->assertEquals($titleEn, $paymentMethod->title_en);
        $this->assertEquals($titleRu, $paymentMethod->title_ru);
    }
}
