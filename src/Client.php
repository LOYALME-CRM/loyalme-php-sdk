<?php

namespace LoyalmeCRM\LoyalmePhpSdk;

use LoyalmeCRM\LoyalmePhpSdk\Api;
use LoyalmeCRM\LoyalmePhpSdk\Exceptions\ClientException;
use LoyalmeCRM\LoyalmePhpSdk\Interfaces\ClientInterface;

class Client extends Api implements ClientInterface
{
    const CLIENT_GENDER_NOT_SELECTED = 0;
    const CLIENT_GENDER_MALE = 1;
    const CLIENT_GENDER_FEMALE = 2;
    const CLIENT_NAME_SUBSCRIBER = 'subscriber';
    const CLIENT_NAME_UNKNOWN = 'unknown';

    const ACTION_CLIENT = 'client';
    const ACTION_CREATE = 'client';
    const ACTION_UPDATE = 'client/%d';
    const ACTION_MERGE_HASH = 'client/%d/merge/%s';
    const ACTION_SHOW = 'client/%d';

    /**
     * @inheritdoc
     */
    protected function _getClassNameException(): string
    {
        return ClientException::class;
    }

    /**
     * @param string $externalId
     * @param string $name
     * @param string $lastName
     * @param string $middleName
     * @param array $birthdate
     * @param null|int $gender
     * @param null|array $phones
     * @param null|array $emails
     * @param array $dateOfRegistered
     * @param array $attributes
     * @return array
     */
    public function prepareDataForSave(
        string $externalId = null,
        string $name = null,
        string $lastName = null,
        string $middleName = null,
        array $birthdate = null,
        ?int $gender = 0,
        ?array $phones = null,
        ?array $emails = null,
        array $dateOfRegistered = null,
        array $attributes
    ): array
    {
        $params = [
            'point_id' => $this->_connection->pointId,
            'brand_id' => $this->_connection->brandId,
            'person_id' => $this->_connection->personId,
            'name' => $name,
        ];

        if (!is_null($gender)) {
            $params['gender'] = $gender;
        }
        if (!is_null($externalId)) {
            $params['external_id'] = $externalId;
        }
        if (!is_null($lastName)) {
            $params['last_name'] = $lastName;
        }
        if (!is_null($middleName)) {
            $params['middle_name'] = $middleName;
        }
        if (!is_null($birthdate)) {
            $params['birthdate_select'] = $birthdate;
        }
        if (!is_null($phones)) {
            $otherPhones = [];
            $otherPhonesSubscribe = [];
            $otherPhonesValidate = [];
            foreach ($phones as $phone) {
                $otherPhones[] = $phone['contact'];
                $otherPhonesSubscribe[] = $phone['subscribe_status'];
                $otherPhonesValidate[] = $phone['validate_status'];
            }
            $params['other_phones'] = $otherPhones;
            $params['other_phones_subscribe'] = $otherPhonesSubscribe;
            $params['other_phones_validate'] = $otherPhonesValidate;
        }
        if (!is_null($emails)) {
            $otherEmails = [];
            $otherEmailsSubscribe = [];
            $otherEmailsValidate = [];
            foreach ($emails as $email) {
                $otherEmails[] = $email['contact'];
                $otherEmailsSubscribe[] = $email['subscribe_status'];
                $otherEmailsValidate[] = $email['validate_status'];
            }
            $params['other_emails'] = $otherEmails;
            $params['other_emails_subscribe'] = $otherEmailsSubscribe;
            $params['other_emails_validate'] = $otherEmailsValidate;
        }
        if (!is_null($dateOfRegistered)) {
            $params['registered_at'] = $dateOfRegistered['year'] . '-' . $dateOfRegistered['month'] . '-' . $dateOfRegistered['day'] . ' ' . $dateOfRegistered['hours'] . ':' . $dateOfRegistered['minutes'] . ':' . $dateOfRegistered['seconds'];
        }
        if ($attributes) {
            foreach ($attributes as $attribute) {
                $params[$attribute['key']] = $attribute['value'];
            }
        }

        return $params;
    }

    /**
     * @param string $externalId
     * @param string $name
     * @param string $lastName
     * @param string $middleName
     * @param string $birthdate - ["day"="", "month"="", "year"=""]
     * @param null|int $gender - Gender of the client. 0 - not selected, 1 - male, 2 - female
     * @param null|array $phones - [["phone"=>"", "subscribe_status"=>0/1, "validate_status"=>1/2/3/4]]
     * @param null|array $emails - [["email"=>"", "subscribe_status"=>0/1, "validate_status"=>1/2/3/4]]
     * @param type $attribute - custom attributes if they exist
     */
    protected function _create(
        string $externalId = null,
        string $name,
        string $lastName = null,
        string $middleName = null,
        array $birthdate = null,
        ?int $gender = 0,
        ?array $phones = null,
        ?array $emails = null,
        array $dateOfRegistered = null,
        ...$attributes
    ): ClientInterface
    {
        $params = $this->prepareDataForSave(
            $externalId, $name, $lastName, $middleName, $birthdate, $gender,
            $phones, $emails, $dateOfRegistered, $attributes
        );
        $result = $this->_connection->sendPostRequest(self::ACTION_CREATE, $params);
        $this->_fill($result);

        return $this;
    }

    /**
     * @param int $id
     * @param string $externalId
     * @param string $name
     * @param string $lastName
     * @param string $middleName
     * @param string $birthdate - ["day"="", "month"="", "year"=""]
     * @param null|int $gender - Gender of the client. 0 - not selected, 1 - male, 2 - female
     * @param null|array $phones - [["phone"=>"", "subscribe_status"=>0/1, "validate_status"=>1/2/3/4]]
     * @param null|array $emails - [["email"=>"", "subscribe_status"=>0/1, "validate_status"=>1/2/3/4]]
     * @param type $attribute - custom attributes if they exist
     */
    protected function _update(
        int $id,
        string $externalId = null,
        string $name = null,
        string $lastName = null,
        string $middleName = null,
        array $birthdate = null,
        ?int $gender = 0,
        ?array $phones = null,
        ?array $emails = null,
        array $dateOfRegistered = null,
        ...$attributes
    ): ClientInterface
    {
        $params = $this->prepareDataForSave(
            $externalId, $name, $lastName, $middleName, $birthdate, $gender,
            $phones, $emails, $dateOfRegistered, $attributes
        );
        $result = $this->_connection->sendPutRequest(sprintf(self::ACTION_UPDATE, $id), $params);
        $this->_fill($result);

        return $this;
    }

    /**
     * @param string $fingerprint
     */
    public function findByFingerprint(string $fingerprint)
    {
        if (empty($fingerprint)) {
            throw new ClientException('Fingerprint not be empty', 400);
        }

        return $this->_connection->sendGetRequest(self::ACTION_CLIENT, [
            'client_hash' => $fingerprint,
        ]);
    }

    /**
     * @param string $externalId
     */
    protected function findByExternalId(string $externalId, bool $fillIn = false)
    {
        $result =  $this->_connection->sendGetRequest(self::ACTION_CLIENT, [
            'external_id' => $externalId,
        ]);

        if ($fillIn && isset($result['data'][0])) {
            $this->_fill(['data' => $result['data'][0]]);
            return $this;
        }

        return $result;
    }

    /**
     * @param string $email
     */
    protected function findByEmail(string $email)
    {
        return $this->_connection->sendGetRequest(self::ACTION_CLIENT, [
            'email' => $email,
        ]);
    }

    /**
     * @param string $phone
     */
    protected function findByPhone(string $phone)
    {
        return $this->_connection->sendGetRequest(self::ACTION_CLIENT, [
            'phone' => $phone,
        ]);
    }

    /**
     * @param int $clientId
     * @param string $fingerprint
     */
    protected function clientMergeHash(int $clientId, string $fingerprint)
    {
        return $this->_connection->sendPostRequest(sprintf(self::ACTION_MERGE_HASH, $clientId, $fingerprint), [
            'id' => $clientId,
            'hash' => $fingerprint,
        ]);
    }

    /**
     * @param int $clientId
     */
    public function getById(int $clientId)
    {
        $result = $this->_connection->sendGetRequest(sprintf(self::ACTION_SHOW, $clientId), []);
        $this->_fill($result);

        return $this;
    }

    /**
     * @param string $externalId
     * @param string $fingerPrint
     * @param string $name
     * @param string $lastName
     * @param string $middleName
     * @param string $birthdate - ["day"="", "month"="", "year"=""]
     * @param null|int $gender - Gender of the client. 0 - not selected, 1 - male, 2 - female
     * @param null|array $phones - [["contact"=>"", "subscribe_status"=>0/1, "validate_status"=>1/2/3/4]]
     * @param null|array $emails - [["contact"=>"", "subscribe_status"=>0/1, "validate_status"=>1/2/3/4]]
     * @param type $attributes - custom attributes if they exist ["key"="", "value"=""]
     */
    public function get(
        string $externalId = null,
        string $fingerPrint = null,
        string $name = null,
        string $lastName = null,
        string $middleName = null,
        array $birthdate = null,
        ?int $gender = 0,
        ?array $phones = null,
        ?array $emails = null,
        array $dateOfRegistered = null,
        ...$attributes
    ): ClientInterface
    {
        if (empty($externalId) && empty($fingerPrint)) {
            throw new ClientException('One of params $externalId or $fingerPrint must be not empty');
        }
        if (is_array($birthdate)) {
            $this->validateArrayStructure(['day', 'month', 'year'], $birthdate, 'birthdate');
        }
        if (is_array($phones)) {
            $this->validateArrayStructure(['contact', 'subscribe_status', 'validate_status'], $phones, 'phones', false);
        }
        if (is_array($emails)) {
            $this->validateArrayStructure(['contact', 'subscribe_status', 'validate_status'], $emails, 'emails', false);
        }
        if (is_array($dateOfRegistered)) {
            $this->validateArrayStructure(['day', 'month', 'year', 'hours', 'minutes', 'seconds'], $dateOfRegistered, 'dateOfRegistered');
        }
        if ($attributes) {
            $this->validateArrayStructure(['key', ['field' => 'value', 'allowEmpty' => true]], $attributes, 'attribute', false);
        }

        $foundByExternalId = false;
        $foundByFingerPrint = false;
        $foundByEmail = false;
        $foundByPhone = false;
        $result = null;
        if ($externalId) {
            $result = $this->findByExternalId($externalId);

            if (isset($result['meta']['pagination']['total']) && $result['meta']['pagination']['total'] > 0) {
                $foundByExternalId = true;
            } else {
                $result = null;
            }
        }
        if ((empty($externalId)) && is_null($result) && $fingerPrint && $name != self::CLIENT_NAME_SUBSCRIBER) {
            $result = $this->findByFingerprint($fingerPrint);

            if (isset($result['meta']['pagination']['total']) && $result['meta']['pagination']['total'] > 0) {
                $foundByFingerPrint = true;
            } else {
                $result = null;
            }
        }
        if (is_null($result) && $emails) {
            foreach ($emails as $email) {
                $result = $this->findByEmail($email['contact']);
                if (isset($result['data'][0]['id'])) {
                    break;
                }
            }

            if (isset($result['meta']['pagination']['total']) && $result['meta']['pagination']['total'] > 0) {
                if ($externalId && $result['data'][0]['external_id']) {
                    $result = null;
                } else {
                    $foundByEmail = true;
                }
            } else {
                $result = null;
            }
        }
        if (is_null($result) && $phones) {
            foreach ($phones as $phone) {
                $result = $this->findByPhone($phone['contact']);
                if (isset($result['data'][0]['id'])) {
                    break;
                }
            }

            if (isset($result['meta']['pagination']['total']) && $result['meta']['pagination']['total'] > 0) {
                if ($externalId && $result['data'][0]['external_id']) {
                    $result = null;
                } else {
                    $foundByPhone = true;
                }
            } else {
                $result = null;
            }
        }
        if (isset($result['meta']['pagination']['total']) && $result['meta']['pagination']['total'] > 1) {
            usort($result['data'], function($a, $b) {
                return strtotime($b['updated_at']['date']) - strtotime($a['updated_at']['date']);
            });
            $result['data'] = [
                $result['data'][0]
            ];
            $result['meta']['pagination']['total'] = 1;
            $result['meta']['pagination']['count'] = 1;
        }
        $params = [
            $externalId, $name, $lastName, $middleName, $birthdate, $gender, $phones,
            $emails, $dateOfRegistered
        ];

        if ($attributes) {
            foreach ($attributes as $attribute) {
                $params[] = $attribute;
            }
        }

        if (is_null($result)) {
            if (empty($name)) {
                throw new ClientException('Param of name is required for new client', 400);
            }
            $client = call_user_func_array([$this, '_create'], $params);
        } else {
            array_unshift($params, $result['data'][0]['id']);
            $client = call_user_func_array([$this, '_update'], $params);
        }

        if ($fingerPrint && !$foundByFingerPrint) {
            $clientHashes = array_column($client->client_hash, 'client_hash');
            if (!in_array($fingerPrint, $clientHashes)) {
                $resultMergeHash = $this->clientMergeHash($client->id, $fingerPrint);
                if ($resultMergeHash['status_code'] == Connection::STATUS_CODE_SUCCESS) {
                    $clientId = $client->id;
                    $client = null;
                    if ($externalId) {
                        $client = $this->findByExternalId($externalId, true);
                    }
                    if (is_null($client)) {
                        $client = $this->getById($clientId);
                    }
                } else {
                    throw new ClientException('Error in answer from API at client merge whith fingerprint', $resultMergeHash['status_code'] ?? 400, $resultMergeHash);
                }
            }
        }

        return $this;
    }

    /**
     * @param string $fingerPrint
     * @param string $email -> ["contact"=>"", "subscribe_status"=>0/1, "validate_status"=>1/2/3/4 (1 - valid, 2 - invalid, 3 - need verification, 4 - stop list)]
     */
    public function subscribeClientByEmail(string $fingerPrint, array $email): ClientInterface
    {
        return $this->get(null, $fingerPrint, self::CLIENT_NAME_SUBSCRIBER, null, null, null, null, null, [$email]);
    }
}
