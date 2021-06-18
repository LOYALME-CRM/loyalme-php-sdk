<?php

use Codeception\Test\Unit;
use LoyalmeCRM\LoyalmePhpSdk\Client;
use LoyalmeCRM\LoyalmePhpSdk\Connection;
use LoyalmeCRM\LoyalmePhpSdk\Exceptions\ClientException;

class ClientTest extends Unit
{
    /**
     * @var UnitTester
     */
    protected $_tester;

    /**
     * @var Connection
     */
    protected $_connection;

    protected function _before()
    {
        $token = 's87lp8G7muL3OMSKcbkgex1lZBxMx52WhA4i9fdYhn9z9GPJS9';
        $apiUrl = 'http://local.loyalme.crm-client/api';
        $brandId = 1;
        $pointId = 6;
        $personId = 3;
        $this->_connection = new Connection($token, $apiUrl, $brandId, $pointId, $personId);
    }

    protected function _after()
    {
        
    }

    public function checkConnectionObject()
    {
        $this->assertTrue($this->_connection instanceof Connection);
    }

    public function testForDevice1()
    {
        $hash = md5(date('Ymdhms') . rand(10000, 99999));
        $externalId = md5(date('Ymdhms') . rand(1000, 9999));
        $phone = rand(10000000000, 99999999999);
        $email = rand(1000000, 9999999) . '@mail.ru';

        $clientRest1 = new Client($this->_connection);
        $client1 = $clientRest1->get(
            null,
            $hash,
            'Неизвестно'
        );

        $this->assertTrue($client1 instanceof Client);

        $clientRest2 = new Client($this->_connection);
        $client2 = $clientRest2->get(
            $externalId,
            $hash,
            'Mike',
            'MikeLN',
            'MikeMN',
            [
                'day' => '02',
                'month' => '12',
                'year' => '1985',
            ],
            0,
            [
                [
                    'contact' => $phone,
                    'subscribe_status' => 1,
                    'validate_status' => 1,
                ]
            ],
            [
                [
                    'contact' => $email,
                    'subscribe_status' => 1,
                    'validate_status' => 1,
                ]
            ],
            'address string',
            '4245',
            '2222222',
            [
                'day' => '02',
                'month' => '12',
                'year' => '2001',
            ],
            'London',
            [
                'day' => '02',
                'month' => '12',
                'year' => '2001',
                'hours' => '19',
                'minutes' => '25',
                'seconds' => '53',
            ],
            ['key' => 'test_field', 'value' => '323232223']
        );

        $this->assertTrue($client2 instanceof Client);
        $this->assertEquals($client1->id + 1, $client2->id);

        $clientRest3 = new Client($this->_connection);
        $client3 = $clientRest3->get(
            null,
            $hash,
            'Неизвестно'
        );

        $this->assertTrue($client3 instanceof Client);
        $this->assertEquals($client2->id, $client3->id);

        $clientRest4 = new Client($this->_connection);
        $client4 = $clientRest4->get(
            $externalId,
            $hash,
            'Mike'
        );

        $this->assertTrue($client4 instanceof Client);
        $this->assertEquals($client3->id, $client4->id);

        $clientRest = new Client($this->_connection);
        $clientsByFingerprint = $clientRest->findByFingerprint($hash);

        $this->assertTrue(isset($clientsByFingerprint['meta']['pagination']['total']));
        $this->assertEquals(1, $clientsByFingerprint['meta']['pagination']['total']);
        $this->assertEquals($externalId, $clientsByFingerprint['data'][0]['external_id']);
        $this->assertEquals('Mike', $clientsByFingerprint['data'][0]['name']);

        try {
            $clientRest5 = new Client($this->_connection);
            $client5 = $clientRest5->getById($client1->id);
        } catch (ClientException $ex) {
            $this->assertEquals(404, $ex->getCode());
        }
    }

    public function testForDevice2()
    {
        $hash = md5(date('Ymdhms') . rand(10000, 99999));
        $externalId1 = md5(date('Ymdhms') . rand(1000, 9999));
        $phone1 = rand(10000000000, 50000000000);
        $email1 = rand(1000000, 5000000) . '@mail.ru';

        $clientRest1 = new Client($this->_connection);
        $client1 = $clientRest1->get(
            null,
            $hash,
            'Неизвестно'
        );

        $this->assertTrue($client1 instanceof Client);

        $clientRest2 = new Client($this->_connection);
        $client2 = $clientRest2->get(
            $externalId1,
            $hash,
            'Mike',
            'MikeLN',
            'MikeMN',
            [
                'day' => '02',
                'month' => '12',
                'year' => '1985',
            ],
            0,
            [
                [
                    'contact' => $phone1,
                    'subscribe_status' => 1,
                    'validate_status' => 1,
                ]
            ],
            [
                [
                    'contact' => $email1,
                    'subscribe_status' => 1,
                    'validate_status' => 1,
                ]
            ],
            'address string',
            '4245',
            '2222222',
            [
                'day' => '02',
                'month' => '12',
                'year' => '2001',
            ],
            'London',
            [
                'day' => '02',
                'month' => '12',
                'year' => '2001',
                'hours' => '19',
                'minutes' => '25',
                'seconds' => '53',
            ],
            ['key' => 'test_field', 'value' => '323232223']
        );

        $this->assertTrue($client2 instanceof Client);
        $this->assertEquals(($client1->id + 1), $client2->id);
        $this->assertEquals($externalId1, $client2->external_id);

        $clientRest3 = new Client($this->_connection);
        $client3 = $clientRest3->get(
            null,
            $hash,
            'Неизвестно'
        );

        $this->assertTrue($client3 instanceof Client);
        $this->assertEquals($client2->id, $client3->id);
        $this->assertEquals($externalId1, $client3->external_id);
        $this->assertEquals('Mike', $client3->name);

        $externalId2 = md5(date('sYmdhm') . rand(1000, 9999));
        $phone2 = rand(50000000001, 60000000000);
        $email2 = rand(5000001, 9999999) . '@mail.ru';

        $clientRest4 = new Client($this->_connection);
        $client4 = $clientRest4->get(
            $externalId2,
            $hash,
            'Mike2',
            'MikeLN2',
            'MikeMN2',
            [
                'day' => '02',
                'month' => '12',
                'year' => '1985',
            ],
            0,
            [
                [
                    'contact' => $phone2,
                    'subscribe_status' => 1,
                    'validate_status' => 1,
                ]
            ],
            [
                [
                    'contact' => $email2,
                    'subscribe_status' => 1,
                    'validate_status' => 1,
                ]
            ],
            'address string more',
            '4245',
            '2222222',
            [
                'day' => '02',
                'month' => '12',
                'year' => '2001',
            ],
            'London',
            [
                'day' => '02',
                'month' => '12',
                'year' => '2001',
                'hours' => '19',
                'minutes' => '25',
                'seconds' => '53',
            ],
            ['key' => 'test_field', 'value' => '323232223']
        );

        $this->assertTrue($client4 instanceof Client);

        $clientRest = new Client($this->_connection);
        $clientsByFingerprint = $clientRest->findByFingerprint($hash);

        $this->assertTrue(isset($clientsByFingerprint['meta']['pagination']['total']));
        $this->assertEquals(2, $clientsByFingerprint['meta']['pagination']['total']);

        $this->assertEquals($externalId1, $clientsByFingerprint['data'][0]['external_id']);
        $this->assertEquals('Mike', $clientsByFingerprint['data'][0]['name']);
        $this->assertEquals($client2->id, $clientsByFingerprint['data'][0]['id']);
        $this->assertEquals($externalId2, $clientsByFingerprint['data'][1]['external_id']);
        $this->assertEquals('Mike2', $clientsByFingerprint['data'][1]['name']);
        $this->assertEquals($client4->id, $clientsByFingerprint['data'][1]['id']);

        try {
            $clientRest5 = new Client($this->_connection);
            $client5 = $clientRest5->getById($client1->id);
        } catch (ClientException $ex) {
            $this->assertEquals(404, $ex->getCode());
        }
    }
}
