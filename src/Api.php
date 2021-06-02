<?php

namespace LoyalmeCRM\LoyalmePhpSdk;

use LoyalmeCRM\LoyalmePhpSdk\Connection;
use LoyalmeCRM\LoyalmePhpSdk\Exceptions\LoyalmePhpSdkException;

abstract class Api
{
    const CLIENT_GENDER_NOT_SELECTED = 0;
    const CLIENT_GENDER_MALE = 1;
    const CLIENT_GENDER_FEMALE = 2;
    /**
     * @var array
     */
    public $attributes;
    /**
     * @var Connection
     */
    protected $_connection;

    /**
     * Api constructor.
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->_connection = $connection;
    }

    /**
     * @param $property
     * @return |null
     */
    public function __get($property)
    {
        $result = null;
        if (isset($this->$property)) {
            $result = $this->$property;
        } elseif (isset($this->attributes->$property)) {
            $result = $this->attributes->$property;
        } elseif (isset($this->attributes[$property])) {
            $result = $this->attributes[$property];
        }
        return $result;
    }

    /**
     * @param array $requiredFields
     * @param array $array
     * @param string $arrayKey
     * @param bool $isItem
     * @throws LoyalmePhpSdkException
     */
    public function validateArrayStructure(array $requiredFields, array $array, string $arrayKey, $isItem = true): void
    {
        if ($isItem) {
            foreach ($requiredFields as $field) {
                $this->_checkAvailabilityField($field, $array, $arrayKey);
            }
        } else {
            foreach ($array as $item) {
                foreach ($requiredFields as $field) {
                    $this->_checkAvailabilityField($field, $item, $arrayKey);
                }
            }
        }
    }

    /**
     * @param string|int $field
     * @param array $array
     * @param string|int $arrayKey
     * @throws LoyalmePhpSdkException
     */
    protected function _checkAvailabilityField($field, array $array, $arrayKey)
    {
        if (is_array($field)) {
            $allowEmpty = $field['allowEmpty'];
            $field = $field['field'];
        } else {
            $allowEmpty = false;
        }
        if ($allowEmpty) {
            if (!array_key_exists($field, $array)) {
                throw new LoyalmePhpSdkException(sprintf('Attribute %s is required for array %s', $field, $arrayKey), 400, $array);
            }
        } else {
            if (!isset($array[$field]) || (empty($array[$field]) && !is_numeric($array[$field]))) {
                throw new LoyalmePhpSdkException(sprintf('Attribute %s is required for array %s', $field, $arrayKey), 400, $array);
            }
        }
    }

    /**
     * @param array $response
     * @return bool
     * @throws LoyalmePhpSdkException
     */
    public function checkResponseForErrors(array $response): bool
    {
        if (isset($response['data'])) {
            return true;
        }
        $classException = $this->getClassNameException();
        if (!isset($response['status_code'])) {
            $result = sprintf('API call error: %s', print_r($response, true));
            throw new $classException($result, 500);
        }
        $message = isset($response['message']) ? $response['message'] : 'API call error. No error message reported from API.';
        $statusCode = isset($response['status_code']);
        $errors = isset($response['errors']) ? $response['errors'] : [];
        throw new $classException($message, $statusCode, $errors);
    }

    /**
     * @return string
     */
    abstract protected function getClassNameException(): string;

    /**
     * @param array $result
     * @return $this
     */
    protected function fill(array $result)
    {
        $this->attributes = [];
        $classNameException = $this->getClassNameException();
        if (isset($result['data'])) {
            foreach ($result['data'] as $field => $value) {
                $this->attributes[$field] = $value;
            }
        } elseif ($result['status_code'] == 200) {
            $this->attributes['result'] = $result;
        } elseif (isset($result['errors']) && $result['errors']) {
            throw new $classNameException('Error operation', $result['status_code'], $result['errors']);
        } else {
            $details = is_array($result) ? json_encode($result) : (string)$result;
            $errorMessage = sprintf('Another exception from API. Details: %s', $details);
            throw new $classNameException($errorMessage, $result['status_code']);
        }

        return $this;
    }
}
