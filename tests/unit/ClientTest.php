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

    public function testOfRegistrationCase1()
    {
        $hash = md5(date('Ymdhms') . rand(10000, 99999));
        $externalId = md5(date('Ymdhms') . rand(1000, 9999));
        $phone = rand(10000000000, 99999999999);
        $email = rand(1000000, 9999999) . '@mail.ru';

        $clientRest1 = new Client($this->_connection);
        $client1 = $clientRest1->get(
            null,
            $hash,
            'Unknown'
        );

        $this->assertTrue($client1 instanceof Client);

        $clientRest2 = new Client($this->_connection);
        $client2 = $clientRest2->get(
            $externalId,
            $hash,
            'Mike',
            'Mikeln',
            'Mikemn',
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
            'Unknown'
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

    public function testOfRegistrationCase2()
    {
        $hash = md5(date('Ymdhms') . rand(10000, 99999));
        $externalId1 = md5(date('Ymdhms') . rand(1000, 9999));
        $phone1 = rand(10000000000, 50000000000);
        $email1 = rand(1000000, 5000000) . '@mail.ru';

        $clientRest1 = new Client($this->_connection);
        $client1 = $clientRest1->get(
            null,
            $hash,
            'Unknown'
        );

        $this->assertTrue($client1 instanceof Client);

        $clientRest2 = new Client($this->_connection);
        $client2 = $clientRest2->get(
            $externalId1,
            $hash,
            'Mike',
            'Mikeln',
            'Mikemn',
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
            'Unknown'
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
            'Mikeln2',
            'Mikemn2',
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

        $clientRest6 = new Client($this->_connection);
        $client6 = $clientRest6->get(
            null,
            $hash,
            'Unknown'
        );

        $this->assertTrue($client6 instanceof Client);
        $this->assertEquals($client4->id, $client6->id);
        $this->assertEquals($externalId2, $client6->external_id);

        $clientRest7 = new Client($this->_connection);
            $client7 = $clientRest7->get(
            $externalId1,
            $hash,
            'Mike'
        );

        $this->assertTrue($client7 instanceof Client);
        $this->assertEquals($client2->id, $client7->id);

        $clientRest8 = new Client($this->_connection);
        $client8 = $clientRest8->get(
            null,
            $hash,
            'Unknown'
        );

        $this->assertTrue($client8 instanceof Client);
        $this->assertEquals($client7->id, $client8->id);
        $this->assertEquals($client7->external_id, $client8->external_id);
        $this->assertEquals('Mike', $client8->name);

        sleep(1);

        $clientRest9 = new Client($this->_connection);
        $client9 = $clientRest9->get(
            $externalId2,
            $hash,
            'Mike2'
        );
        $this->assertTrue($client9 instanceof Client);
        $this->assertEquals($client4->id, $client9->id);

        $clientRest10 = new Client($this->_connection);
        $client10 = $clientRest10->get(
            null,
            $hash,
            'Unknown'
        );

        $this->assertTrue($client10 instanceof Client);
        $this->assertEquals($client9->id, $client10->id);
        $this->assertEquals($client9->external_id, $client10->external_id);
        $this->assertEquals('Mike2', $client10->name);
    }

    public function testOfRegistrationCase3()
    {
        $hash1 = md5(date('Ymdhms') . rand(10000, 30000));
        $externalId1 = md5(date('Ymdhms') . rand(1000, 3000));
        $phone1 = rand(10000000000, 30000000000);
        $email1 = rand(1000000, 3000000) . '@mail.ru';

        $clientRest1 = new Client($this->_connection);
        $client1 = $clientRest1->get(
            $externalId1,
            $hash1,
            'Mike',
            'Mikeln',
            'Mikemn',
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

        $this->assertTrue($client1 instanceof Client);

        $hash2 = md5(date('Ymdhms') . rand(30001, 60000));

        $clientRest2 = new Client($this->_connection);
        $client2 = $clientRest2->get(
            null,
            $hash2,
            'Unknown'
        );

        $this->assertTrue($client2 instanceof Client);
        $this->assertEquals($client1->id +1, $client2->id);

        $clientRest3 = new Client($this->_connection);
        $client3 = $clientRest3->get(
            $externalId1,
            $hash2,
            'Mike2'
        );

        $this->assertTrue($client3 instanceof Client);
        $this->assertEquals($client1->id + 1, $client3->id);
        $this->assertEquals($client2->id, $client3->id);
        $this->assertEquals('Mike2', $client3->name);
        $this->assertEquals('Mikeln', $client3->last_name);

        try {
            $clientRest4 = new Client($this->_connection);
            $client4 = $clientRest4->getById($client1->id);
        } catch (ClientException $ex) {
            $this->assertEquals(404, $ex->getCode());
        }

        $clientRest5 = new Client($this->_connection);
        $client5 = $clientRest5->get(
            null,
            $hash2,
            'Unknown'
        );

        $this->assertTrue($client5 instanceof Client);
        $this->assertEquals($client5->id, $client3->id);

        $externalId2 = md5(date('Ymdhms') . rand(3001, 6000));
        $phone2 = rand(30000000001, 60000000000);
        $email2 = rand(3000001, 6000000) . '@mail.ru';

        $clientRest6 = new Client($this->_connection);
        $client6 = $clientRest6->get(
            $externalId2,
            $hash2,
            'Mike3',
            'Mikeln3',
            'Mikemn3',
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

        $this->assertTrue($client6 instanceof Client);
        $this->assertEquals($client6->id, $client3->id + 1);

        $clientRest7 = new Client($this->_connection);
        $client7 = $clientRest7->getById($client5->id);

        $this->assertTrue($client7 instanceof Client);
        $this->assertEquals($client7->id, $client3->id);

        $hash3 = md5(date('sYmdhm') . rand(60001, 90000));

        $clientRest8 = new Client($this->_connection);
        $client8 = $clientRest8->get(
            null,
            $hash3,
            'Unknown'
        );

        $this->assertTrue($client8 instanceof Client);
        $this->assertEquals($client8->id, $client6->id + 1);

        $clientRest9 = new Client($this->_connection);
        $client9 = $clientRest9->get(
            $externalId2,
            $hash3,
            'Mike4'
        );

        $this->assertTrue($client8 instanceof Client);
        $this->assertEquals($client9->id, $client8->id);
        $this->assertEquals('Mike4', $client9->name);
        $this->assertEquals('Mikeln3', $client9->last_name);

        try {
            $clientRest10 = new Client($this->_connection);
            $client10 = $clientRest10->getById($client6->id);
        } catch (ClientException $ex) {
            $this->assertEquals(404, $ex->getCode());
        }

        $clientRest11 = new Client($this->_connection);
        $client11 = $clientRest11->get(
            null,
            $hash3,
            'Unknown'
        );

        $this->assertTrue($client11 instanceof Client);
        $this->assertEquals($client11->id, $client9->id);

        $externalId3 = md5(date('Ymdhms') . rand(3001, 6000));
        $phone3 = rand(60000000001, 99999999999);
        $email3 = rand(6000001, 9999999) . '@mail.ru';

        $clientRest12 = new Client($this->_connection);
        $client12 = $clientRest12->get(
            $externalId3,
            $hash3,
            'Mike5',
            'Mikeln5',
            'Mikemn5',
            [
                'day' => '02',
                'month' => '12',
                'year' => '1985',
            ],
            0,
            [
                [
                    'contact' => $phone3,
                    'subscribe_status' => 1,
                    'validate_status' => 1,
                ]
            ],
            [
                [
                    'contact' => $email3,
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

        $this->assertTrue($client12 instanceof Client);
        $this->assertEquals($client12->id, $client11->id + 1);

        $clientRest = new Client($this->_connection);
        $clientsByFingerprint1 = $clientRest->findByFingerprint($hash1);

        $this->assertEquals(1, $clientsByFingerprint1['meta']['pagination']['total']);
        $this->assertEquals($client7->id, $clientsByFingerprint1['data'][0]['id']);
        $this->assertEquals($externalId1, $clientsByFingerprint1['data'][0]['external_id']);

        $clientsByFingerprint2 = $clientRest->findByFingerprint($hash2);
        $this->assertEquals(2, $clientsByFingerprint2['meta']['pagination']['total']);

        foreach ($clientsByFingerprint2['data'] as $client) {
            if ($client['id'] == $client7->id) {
                $clientHashes = array_column($client['client_hash'], 'client_hash');
                $this->assertEquals(2, count($clientHashes));
                $this->assertTrue(in_array($hash1, $clientHashes));
                $this->assertTrue(in_array($hash2, $clientHashes));
                $this->assertEquals($externalId1, $client['external_id']);
            } elseif ($client['id'] == $client9->id) {
                $clientHashes = array_column($client['client_hash'], 'client_hash');
                $this->assertEquals(2, count($clientHashes));
                $this->assertTrue(in_array($hash2, $clientHashes));
                $this->assertTrue(in_array($hash3, $clientHashes));
                $this->assertEquals($externalId2, $client['external_id']);
            } else {
                $this->assertTrue(false);
            }
        }

        $clientsByFingerprint3 = $clientRest->findByFingerprint($hash3);
        $this->assertEquals(2, $clientsByFingerprint3['meta']['pagination']['total']);

        foreach ($clientsByFingerprint3['data'] as $client) {
            if ($client['id'] == $client12->id) {
                $clientHashes = array_column($client['client_hash'], 'client_hash');
                $this->assertEquals(1, count($clientHashes));
                $this->assertTrue(in_array($hash3, $clientHashes));
                $this->assertEquals($externalId3, $client['external_id']);
            } elseif ($client['id'] == $client9->id) {
                $clientHashes = array_column($client['client_hash'], 'client_hash');
                $this->assertEquals(2, count($clientHashes));
                $this->assertTrue(in_array($hash2, $clientHashes));
                $this->assertTrue(in_array($hash3, $clientHashes));
                $this->assertEquals($externalId2, $client['external_id']);
            } else {
                $this->assertTrue(false);
            }
        }
    }

    public function testOfRegistrationCase4()
    {
        $hash1 = md5(date('Ymdhms') . rand(10000, 99999));
        $externalId1 = md5(date('Ymdhms') . rand(1000, 9999));
        $email1 = rand(1000000, 5000000) . '@mail.ru';

        $clientRest1 = new Client($this->_connection);
        $client1 = $clientRest1->get(
            null,
            $hash1,
            Client::CLIENT_NAME_UNKNOWN
        );

        $this->assertTrue($client1 instanceof Client);

        $clientRest2 = new Client($this->_connection);
        $client2 = $clientRest2->subscribeClientByEmail(
            $hash1,
            ['contact' => $email1, 'subscribe_status' => 1, 'validate_status' => 1]
        );

        $this->assertTrue($client2 instanceof Client);

        try {
            $clientRest3 = new Client($this->_connection);
            $client3 = $clientRest3->getById($client1->id);
        } catch (ClientException $ex) {
            $this->assertEquals(404, $ex->getCode());
        }

        $clientRest = new Client($this->_connection);
        $clientsByFingerprint1 = $clientRest->findByFingerprint($hash1);
        $this->assertEquals(1, $clientsByFingerprint1['meta']['pagination']['total']);

        foreach ($clientsByFingerprint1['data'] as $client) {
            if ($client['id'] == $client2->id) {
                $this->assertEquals(mb_strtolower(Client::CLIENT_NAME_SUBSCRIBER), mb_strtolower($client['name']));
            } else {
                $this->assertTrue(false);
            }
        }

        $externalId1 = md5(date('Ymdhms') . rand(1000, 3000));

        $clientRest4 = new Client($this->_connection);
        $client4 = $clientRest4->get(
            $externalId1,
            $hash1,
            'Mike3',
            'Mikeln3',
            'Mikemn3',
            [
                'day' => '02',
                'month' => '12',
                'year' => '1985',
            ],
            0,
            null,
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

        $this->assertTrue($client4 instanceof Client);
        $this->assertEquals($client2->id, $client4->id);
        $this->assertEquals('Mike3', $client4->name);
        $this->assertEquals('Mikeln3', $client4->last_name);
    }

    public function testOfRegistrationCase5()
    {
        $hash1 = md5(date('Ymdhms') . rand(10000, 99999));
        $externalId1 = md5(date('Ymdhms') . rand(1000, 9999));
        $email1 = rand(1000000, 5000000) . '@mail.ru';

        $clientRest1 = new Client($this->_connection);
        $client1 = $clientRest1->get(
            null,
            $hash1,
            Client::CLIENT_NAME_UNKNOWN
        );

        $this->assertTrue($client1 instanceof Client);

        $clientRest2 = new Client($this->_connection);
        $client2 = $clientRest2->subscribeClientByEmail(
            $hash1,
            ['contact' => $email1, 'subscribe_status' => 1, 'validate_status' => 1]
        );

        $this->assertTrue($client2 instanceof Client);

        try {
            $clientRest3 = new Client($this->_connection);
            $client3 = $clientRest3->getById($client1->id);
        } catch (ClientException $ex) {
            $this->assertEquals(404, $ex->getCode());
        }

        $clientRest = new Client($this->_connection);
        $clientsByFingerprint1 = $clientRest->findByFingerprint($hash1);
        $this->assertEquals(1, $clientsByFingerprint1['meta']['pagination']['total']);

        foreach ($clientsByFingerprint1['data'] as $client) {
            if ($client['id'] == $client2->id) {
                $this->assertEquals(mb_strtolower(Client::CLIENT_NAME_SUBSCRIBER), mb_strtolower($client['name']));
            } else {
                $this->assertTrue(false);
            }
        }

        $externalId1 = md5(date('Ymdhms') . rand(1000, 3000));
        $email2 = rand(1000000, 5000000) . '@mail.ru';

        $clientRest4 = new Client($this->_connection);
        $client4 = $clientRest4->get(
            $externalId1,
            $hash1,
            'Mike3',
            'Mikeln3',
            'Mikemn3',
            [
                'day' => '02',
                'month' => '12',
                'year' => '1985',
            ],
            0,
            null,
            [
                [
                    'contact' => $email2,
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

        $this->assertTrue($client4 instanceof Client);
        $this->assertEquals($client2->id + 1, $client4->id);
        $this->assertEquals('Mike3', $client4->name);
        $this->assertEquals('Mikeln3', $client4->last_name);
        $this->assertEquals(1, count($client4->emails));
        $this->assertEquals($email2, $client4->emails[0]['email']);

        $clientRest5 = new Client($this->_connection);
        $client5 = $clientRest5->getById($client2->id);

        $this->assertEquals($client2->id, $client5->id);
        $this->assertEquals(mb_strtolower(Client::CLIENT_NAME_SUBSCRIBER), mb_strtolower($client5->name));

        $externalId2 = md5(date('Ymdhms') . rand(3001, 6000));
        $clientRest6 = new Client($this->_connection);
        $client6 = $clientRest6->get(
            $externalId2,
            $hash1,
            'Mike4',
            'Mikeln4',
            'Mikemn4',
            [
                'day' => '02',
                'month' => '12',
                'year' => '1985',
            ],
            0,
            null,
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

        $this->assertTrue($client6 instanceof Client);
        $this->assertEquals('Mike4', $client6->name);
        $this->assertEquals('Mikeln4', $client6->last_name);
        $this->assertEquals(1, count($client6->emails));
        $this->assertEquals($email1, $client6->emails[0]['email']);

        try {
            $clientRest7 = new Client($this->_connection);
            $client7 = $clientRest3->getById($client5->id);
        } catch (ClientException $ex) {
            $this->assertEquals(404, $ex->getCode());
        }
    }

    public function testOfRegistrationCase6()
    {
        $hash1 = md5(date('Ymdhms') . rand(10000, 99999));
        $email1 = rand(1000000, 5000000) . '@mail.ru';

        $clientRest1 = new Client($this->_connection);
        $client1 = $clientRest1->get(
            null,
            $hash1,
            Client::CLIENT_NAME_UNKNOWN
        );

        $this->assertTrue($client1 instanceof Client);

        $clientRest2 = new Client($this->_connection);
        $client2 = $clientRest2->subscribeClientByEmail(
            $hash1,
            ['contact' => $email1, 'subscribe_status' => 1, 'validate_status' => 1]
        );

        $this->assertTrue($client2 instanceof Client);

        try {
            $clientRest3 = new Client($this->_connection);
            $client3 = $clientRest3->getById($client1->id);
        } catch (ClientException $ex) {
            $this->assertEquals(404, $ex->getCode());
        }

        $clientRest = new Client($this->_connection);
        $clientsByFingerprint1 = $clientRest->findByFingerprint($hash1);
        $this->assertEquals(1, $clientsByFingerprint1['meta']['pagination']['total']);

        foreach ($clientsByFingerprint1['data'] as $client) {
            if ($client['id'] == $client2->id) {
                $this->assertEquals(mb_strtolower(Client::CLIENT_NAME_SUBSCRIBER), mb_strtolower($client['name']));
            } else {
                $this->assertTrue(false);
            }
        }

        $externalId1 = md5(date('Ymdhms') . rand(1000, 3000));
        $hash2 = md5(date('Ymdhms') . rand(10000, 99999));

        $clientRest4 = new Client($this->_connection);
        $client4 = $clientRest4->get(
            null,
            $hash2,
            Client::CLIENT_NAME_UNKNOWN
        );

        $this->assertTrue($client4 instanceof Client);

        $clientRest5 = new Client($this->_connection);
        $client5 = $clientRest5->get(
            $externalId1,
            $hash2,
            'Mike4',
            'Mikeln4',
            'Mikemn4',
            [
                'day' => '02',
                'month' => '12',
                'year' => '1985',
            ],
            0,
            null,
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

        try {
            $clientRest6 = new Client($this->_connection);
            $client6 = $clientRest6->getById($client2->id);
        } catch (ClientException $ex) {
            $this->assertEquals(404, $ex->getCode());
        }

        try {
            $clientRest7 = new Client($this->_connection);
            $client7 = $clientRest7->getById($client4->id);
        } catch (ClientException $ex) {
            $this->assertEquals(404, $ex->getCode());
        }

        $clientsByFingerprint1 = $clientRest->findByFingerprint($hash1);
        $this->assertEquals(1, $clientsByFingerprint1['meta']['pagination']['total']);

        $clientsByFingerprint2 = $clientRest->findByFingerprint($hash2);
        $this->assertEquals(1, $clientsByFingerprint2['meta']['pagination']['total']);

        $this->assertEquals($clientsByFingerprint1['data'][0]['id'], $clientsByFingerprint2['data'][0]['id']);
    }

    public function testOfClientUnsubscribe()
    {
        $hash1 = md5(date('Ymdhms') . rand(10000, 30000));
        $externalId1 = md5(date('Ymdhms') . rand(1000, 3000));
        $phone1 = rand(10000000000, 30000000000);
        $email1 = rand(1000000, 3000000) . '@mail.ru';

        $clientRest1 = new Client($this->_connection);
        $client1 = $clientRest1->get(
            $externalId1,
            $hash1,
            'Mike',
            'Mikeln',
            'Mikemn',
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

        $this->assertTrue($client1 instanceof Client);
        $this->assertEquals($client1->phones[0]['phone'], $phone1);
        $this->assertEquals($client1->phones[0]['subscription_status'], 1);
        $this->assertEquals($client1->phones[0]['status'], 1);
        $this->assertEquals($client1->emails[0]['email'], $email1);
        $this->assertEquals($client1->emails[0]['subscription_status'], 1);
        $this->assertEquals($client1->emails[0]['status'], 1);

        $clientRest2 = new Client($this->_connection);
        $client2 = $clientRest2->get(
            $externalId1,
            $hash1,
            'Mike',
            null,
            null,
            null,
            null,
            [
                [
                    'contact' => $phone1,
                    'subscribe_status' => 0,
                    'validate_status' => 1,
                ]
            ],
            [
                [
                    'contact' => $email1,
                    'subscribe_status' => 0,
                    'validate_status' => 1,
                ]
            ]
        );

        $this->assertTrue($client2 instanceof Client);
        $this->assertEquals($client2->last_name, 'Mikeln');
        $this->assertEquals($client2->middle_name, 'Mikemn');
        $this->assertEquals($client2->birthdate['date'], '1985-12-02 00:00:00.000000');
        $this->assertEquals($client2->gender, 0);
        $this->assertEquals($client2->phones[0]['phone'], $phone1);
        $this->assertEquals($client2->phones[0]['subscription_status'], 2);
        $this->assertEquals($client2->phones[0]['status'], 1);
        $this->assertEquals($client2->emails[0]['email'], $email1);
        $this->assertEquals($client2->emails[0]['subscription_status'], 2);
        $this->assertEquals($client2->emails[0]['status'], 1);
    }

    public function testOfClientRegistrationWithoutSubscribe()
    {
        $hash1 = md5(date('Ymdhms') . rand(10000, 30000));
        $externalId1 = md5(date('Ymdhms') . rand(1000, 3000));
        $phone1 = rand(10000000000, 30000000000);
        $email1 = rand(1000000, 3000000) . '@mail.ru';

        $clientRest1 = new Client($this->_connection);
        $client1 = $clientRest1->get(
            $externalId1,
            $hash1,
            'Mike',
            'Mikeln',
            'Mikemn',
            [
                'day' => '02',
                'month' => '12',
                'year' => '1985',
            ],
            0,
            [
                [
                    'contact' => $phone1,
                    'subscribe_status' => 0,
                    'validate_status' => 1,
                ]
            ],
            [
                [
                    'contact' => $email1,
                    'subscribe_status' => 0,
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

        $this->assertTrue($client1 instanceof Client);
        $this->assertEquals($client1->phones[0]['phone'], $phone1);
        $this->assertEquals($client1->phones[0]['subscription_status'], 3);
        $this->assertEquals($client1->phones[0]['status'], 1);
        $this->assertEquals($client1->emails[0]['email'], $email1);
        $this->assertEquals($client1->emails[0]['subscription_status'], 3);
        $this->assertEquals($client1->emails[0]['status'], 1);

        $clientRest2 = new Client($this->_connection);
        $client2 = $clientRest2->get(
            $externalId1,
            $hash1,
            'Mike',
            null,
            null,
            null,
            null,
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
            ]
        );

        $this->assertTrue($client2 instanceof Client);
        $this->assertEquals($client2->last_name, 'Mikeln');
        $this->assertEquals($client2->middle_name, 'Mikemn');
        $this->assertEquals($client2->birthdate['date'], '1985-12-02 00:00:00.000000');
        $this->assertEquals($client2->gender, 0);
        $this->assertEquals($client2->phones[0]['phone'], $phone1);
        $this->assertEquals($client2->phones[0]['subscription_status'], 1);
        $this->assertEquals($client2->phones[0]['status'], 1);
        $this->assertEquals($client2->emails[0]['email'], $email1);
        $this->assertEquals($client2->emails[0]['subscription_status'], 1);
        $this->assertEquals($client2->emails[0]['status'], 1);

        $clientRest3 = new Client($this->_connection);
        $client3 = $clientRest3->get(
            $externalId1,
            $hash1,
            'Mike',
            null,
            null,
            null,
            null,
            [
                [
                    'contact' => $phone1,
                    'subscribe_status' => 0,
                    'validate_status' => 1,
                ]
            ],
            [
                [
                    'contact' => $email1,
                    'subscribe_status' => 0,
                    'validate_status' => 1,
                ]
            ]
        );

        $this->assertTrue($client3 instanceof Client);
        $this->assertEquals($client3->last_name, 'Mikeln');
        $this->assertEquals($client3->middle_name, 'Mikemn');
        $this->assertEquals($client3->birthdate['date'], '1985-12-02 00:00:00.000000');
        $this->assertEquals($client3->gender, 0);
        $this->assertEquals($client3->phones[0]['phone'], $phone1);
        $this->assertEquals($client3->phones[0]['subscription_status'], 2);
        $this->assertEquals($client3->phones[0]['status'], 1);
        $this->assertEquals($client3->emails[0]['email'], $email1);
        $this->assertEquals($client3->emails[0]['subscription_status'], 2);
        $this->assertEquals($client3->emails[0]['status'], 1);
    }
}
