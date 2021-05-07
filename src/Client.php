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

    const ACTION_CLIENT = 'client';
    const ACTION_CREATE = 'client';
    const ACTION_UPDATE = 'client/%d';
    const ACTION_MERGE_HASH = 'client/%d/merge/%s';
    const ACTION_SHOW = 'client/%d';

    /**
     * @inheritdoc
     */
    protected function getClassNameException(): string
    {
        return ClientException::class;
    }

    /**
     * @param string $externalId
     * @param string $name
     * @param string $lastName
     * @param string $middleName
     * @param array $birthdate
     * @param int $gender
     * @param array $phones
     * @param array $emails
     * @param string $address
     * @param string $passportSeria
     * @param string $passportNumber
     * @param array $passportIssuedDate
     * @param string $passportIssuedBy
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
        int $gender = 0,
        array $phones = null,
        array $emails = null,
        string $address = null,
        string $passportSeria = null,
        string $passportNumber = null,
        array $passportIssuedDate = null,
        string $passportIssuedBy = null,
        array $dateOfRegistered = null,
        array $attributes
    ): array
    {
        $params = [
            'point_id' => $this->_connection->pointId,
            'brand_id' => $this->_connection->brandId,
            'person_id' => $this->_connection->personId,
            'name' => $name,
            'gender' => $gender
        ];

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
        if (!is_null($address)) {
            $params['address'] = $address;
        }
        if (!is_null($passportSeria)) {
            $params['passport_seria'] = $passportSeria;
        }
        if (!is_null($passportNumber)) {
            $params['passport_number'] = $passportNumber;
        }
        if (!is_null($passportIssuedDate)) {
            $params['passport_issued_date_select'] = $passportIssuedDate;
        }
        if (!is_null($passportIssuedBy)) {
            $params['passport_issued_by'] = $passportIssuedBy;
        }
        if (!is_null($dateOfRegistered)) {
            $params['date_of_registered'] = $dateOfRegistered;
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
     * @param string $gender - Gender of the client. 0 - not selected, 1 - male, 2 - female
     * @param array $phones - [["phone"=>"", "subscribe_status"=>0/1, "validate_status"=>1/2/3/4]]
     * @param array $emails - [["email"=>"", "subscribe_status"=>0/1, "validate_status"=>1/2/3/4]]
     * @param array $address
     * @param array $passportSeria
     * @param array $passportNumber
     * @param array $passportIssuedDate
     * @param array $passportIssuedBy
     * @param type $attribute - custom attributes if they exist
     */
    protected function create(
        string $externalId = null,
        string $name,
        string $lastName = null,
        string $middleName = null,
        array $birthdate = null,
        int $gender = 0,
        array $phones = null,
        array $emails = null,
        string $address = null,
        string $passportSeria = null,
        string $passportNumber = null,
        array $passportIssuedDate = null,
        string $passportIssuedBy = null,
        array $dateOfRegistered = null,
        ...$attributes
    ): ClientInterface
    {
        $params = $this->prepareDataForSave(
            $externalId, $name, $lastName, $middleName, $birthdate, $gender,
            $phones, $emails, $address, $passportSeria, $passportNumber,
            $passportIssuedDate, $passportIssuedBy, $dateOfRegistered, $attributes
        );
        $result = $this->_connection->sendPostRequest(self::ACTION_CREATE, $params);
        $this->fill($result);

        return $this;
    }

    /**
     * @param int $id
     * @param string $externalId
     * @param string $name
     * @param string $lastName
     * @param string $middleName
     * @param string $birthdate - ["day"="", "month"="", "year"=""]
     * @param string $gender - Gender of the client. 0 - not selected, 1 - male, 2 - female
     * @param array $phones - [["phone"=>"", "subscribe_status"=>0/1, "validate_status"=>1/2/3/4]]
     * @param array $emails - [["email"=>"", "subscribe_status"=>0/1, "validate_status"=>1/2/3/4]]
     * @param array $address
     * @param array $passportSeria
     * @param array $passportNumber
     * @param array $passportIssuedDate
     * @param array $passportIssuedBy
     * @param type $attribute - custom attributes if they exist
     */
    protected function update(
        int $id,
        string $externalId = null,
        string $name = null,
        string $lastName = null,
        string $middleName = null,
        array $birthdate = null,
        int $gender = 0,
        array $phones = null,
        array $emails = null,
        string $address = null,
        string $passportSeria = null,
        string $passportNumber = null,
        array $passportIssuedDate = null,
        string $passportIssuedBy = null,
        array $dateOfRegistered = null,
        ...$attributes
    ): ClientInterface
    {
        $params = $this->prepareDataForSave(
            $externalId, $name, $lastName, $middleName, $birthdate, $gender,
            $phones, $emails, $address, $passportSeria, $passportNumber,
            $passportIssuedDate, $passportIssuedBy, $dateOfRegistered, $attributes
        );
        $result = $this->_connection->sendPutRequest(sprintf(self::ACTION_UPDATE, $id), $params);
        $this->fill($result);

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
    protected function findByExternalId(string $externalId)
    {
        return $this->_connection->sendGetRequest(self::ACTION_CLIENT, [
            'external_id' => $externalId,
        ]);
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
    protected function getById(int $clientId)
    {
        $result = $this->_connection->sendGetRequest(sprintf(self::ACTION_SHOW, $clientId), []);
        $this->fill($result);

        return $this;
    }

    /**
     * @param string $externalId
     * @param string $fingerPrint
     * @param string $name
     * @param string $lastName
     * @param string $middleName
     * @param string $birthdate - ["day"="", "month"="", "year"=""]
     * @param string $gender - Gender of the client. 0 - not selected, 1 - male, 2 - female
     * @param array $phones - [["contact"=>"", "subscribe_status"=>0/1, "validate_status"=>1/2/3/4]]
     * @param array $emails - [["contact"=>"", "subscribe_status"=>0/1, "validate_status"=>1/2/3/4]]
     * @param array $address
     * @param array $passportSeria
     * @param array $passportNumber
     * @param array $passportIssuedDate - ["day"="", "month"="", "year"=""]
     * @param array $passportIssuedBy
     * @param type $attributes - custom attributes if they exist ["key"="", "value"=""]
     */
    public function get(
        string $externalId = null,
        string $fingerPrint = null,
        string $name = null,
        string $lastName = null,
        string $middleName = null,
        array $birthdate = null,
        int $gender = 0,
        array $phones = null,
        array $emails = null,
        string $address = null,
        string $passportSeria = null,
        string $passportNumber = null,
        array $passportIssuedDate = null,
        string $passportIssuedBy = null,
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
        if (is_array($passportIssuedDate)) {
            $this->validateArrayStructure(['day', 'month', 'year'], $passportIssuedDate, 'passportIssuedDate');
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
            if (isset($result['status_code']) && $result['status_code'] == Connection::STATUS_CODE_NOT_FOUND) {
                $result = null;
            } else {
                $foundByExternalId = true;
            }
        }
        if (is_null($result) && $fingerPrint) {
            $result = $this->findByFingerprint($fingerPrint);
            if (isset($result['status_code']) && $result['status_code'] == Connection::STATUS_CODE_NOT_FOUND) {
                $result = null;
            } else {
                $foundByFingerPrint = true;
            }
        }
        if (is_null($result) && $emails) {
            foreach ($emails as $email) {
                $result = $this->findByEmail($email['contact']);
                if (isset($result['data'][0]['id'])) {
                    break;
                }
            }
            if (isset($result['status_code']) && $result['status_code'] == Connection::STATUS_CODE_NOT_FOUND) {
                $result = null;
            } else {
                $foundByEmail = true;
            }
        }
        if (is_null($result) && $phones) {
            foreach ($phones as $phone) {
                $result = $this->findByPhone($phone['contact']);
                if (isset($result['data'][0]['id'])) {
                    break;
                }
            }
            if (isset($result['status_code']) && $result['status_code'] == Connection::STATUS_CODE_NOT_FOUND) {
                $result = null;
            } else {
                $foundByPhone = true;
            }
        }
        if (isset($result['meta']['pagination']['total']) && $result['meta']['pagination']['total'] > 1) {
            throw new ClientException('Searching returned more than one result', 400, $result);
        }
        $params = [
            $externalId, $name, $lastName, $middleName, $birthdate, $gender, $phones,
            $emails, $address, $passportSeria, $passportNumber, $passportIssuedDate, 
            $passportIssuedBy, $dateOfRegistered
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
            $client = call_user_func_array([$this, 'create'], $params);
        } else {
            array_unshift($params, $result['data'][0]['id']);
            $client = call_user_func_array([$this, 'update'], $params);
        }

        if ($fingerPrint && !$foundByFingerPrint) {
            $clientHashes = array_column($client->client_hash, 'client_hash');
            if (!in_array($fingerPrint, $clientHashes)) {
                $resultMergeHash = $this->clientMergeHash($client->id, $fingerPrint);
                if ($resultMergeHash['status_code'] == Connection::STATUS_CODE_SUCCESS) {
                    $client = $this->getById($client->id);
                } else {
                    throw new ClientException('Error in answer from API at client merge whit fingerprint', $resultMergeHash['status_code'] ?? 400, $resultMergeHash);
                }
            }
        }

        return $this;
    }

    /**
     * @param string $fingerPrint
     * @param string $email -> ["contact"=>"", "subscribe_status"=>0/1, "validate_status"=>1/2/3/4]
     */
    public function subscribeClientByEmail(string $fingerPrint, array $email): ClientInterface
    {
        $this->validateArrayStructure(['contact', 'subscribe_status', 'validate_status'], $email, 'email');

        $result = $this->findByFingerprint($fingerPrint);
        if (isset($result['status_code']) && $result['status_code'] == Connection::STATUS_CODE_NOT_FOUND) {
            $result = null;
        }
        if (is_null($result)) {
            $result = $this->findByEmail($email['contact']);
            if (isset($result['status_code']) && $result['status_code'] == Connection::STATUS_CODE_NOT_FOUND) {
                $result = null;
            }
        }
        if (isset($result['meta']['pagination']['total']) && $result['meta']['pagination']['total'] > 1) {
            throw new ClientException('Searching returned more than one result', 400, $result);
        }
        if (is_null($result)) {
            $client = $this->create(null, 'subscriber', null, null, null, self::CLIENT_GENDER_NOT_SELECTED, null, [$email]);
        } else {
            $emails = [$email];
            if (isset($result['data'][0]['id'])) {
                foreach ($result['data'][0]['emails'] as $existingEmail) {
                    if ($existingEmail['email'] != $email['contact']) {
                        $emails[] = [
                            'contact' => $existingEmail['email'],
                            'subscribe_status' => $existingEmail['subscription_status'] == 1 ? 1 : 0,
                            'validate_status' => $existingEmail['status'],
                        ];
                    }
                }
                $client = $this->update($result['data'][0]['id'], $result['data'][0]['external_id'], $result['data'][0]['name'], null, null, null, $result['data'][0]['gender'], null, $emails);
            } else {
                throw new ClientException('Unknow error in response from API', 400, $result);
            }
        }

        $clientHashes = array_column($client->client_hash, 'client_hash');
        if (!in_array($fingerPrint, $clientHashes)) {
            $resultMergeHash = $this->clientMergeHash($client->id, $fingerPrint);
            if ($resultMergeHash['status_code'] == Connection::STATUS_CODE_SUCCESS) {
                $client = $this->getById($client->id);
            } else {
                throw new ClientException('Error in answer from API at client merge whit fingerprint', $resultMergeHash['status_code'] ?? 400, $resultMergeHash);
            }
        }

        return $this;
    }
}
