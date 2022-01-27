<?php

use Codeception\Test\Unit;
use LoyalmeCRM\LoyalmePhpSdk\PaymentStatus;
use LoyalmeCRM\LoyalmePhpSdk\Connection;

class PaymentStatusTest extends Unit
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

    public function testCreatingPaymentStatus()
    {
        $salt = rand(1000, 9999);
        $slug = 'PaymentStatusSlug' . $salt;
        $titleEn = 'PaymentStatusEn' . $salt;
        $titleRu = 'PaymentStatusRu' . $salt;

        $paymentStatusObject = new PaymentStatus($this->_connection);
        $paymentStatus = $paymentStatusObject->get($slug, $titleEn, $titleRu);

        $this->assertTrue($paymentStatusObject instanceof PaymentStatus);
        $this->assertEquals($slug, $paymentStatus->slug);
        $this->assertEquals($titleEn, $paymentStatus->title_en);
        $this->assertEquals($titleRu, $paymentStatus->title_ru);

        $titleEn = 'PaymentStatusEn2' . $salt;
        $titleRu = 'PaymentStatusRu2' . $salt;

        $paymentStatusObject = new PaymentStatus($this->_connection);
        $paymentStatus = $paymentStatusObject->get($slug, $titleEn, $titleRu);

        $this->assertTrue($paymentStatusObject instanceof PaymentStatus);
        $this->assertEquals($slug, $paymentStatus->slug);
        $this->assertEquals($titleEn, $paymentStatus->title_en);
        $this->assertEquals($titleRu, $paymentStatus->title_ru);
    }
}
