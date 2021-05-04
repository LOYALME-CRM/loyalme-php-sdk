<?php

namespace LoyalmeCRM\LoyalmePhpSdk;

use LoyalmeCRM\LoyalmePhpSdk\Connection;
use LoyalmeCRM\LoyalmePhpSdk\Exceptions\LoyalmePhpSdkException;

class Api
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
     * @param \LoyalmeCRM\LoyalmePhpSdk\Connection $connection
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
            if (!isset($array[$field]) || empty($array[$field])) {
                throw new LoyalmePhpSdkException(sprintf('Attribute %s is required for array %s', $field, $arrayKey), 400, $array);
            }
        }
    }

    public function fill(array $result)
    {
        $this->attributes = [];
        $classNameException = $this->getClassNameException();
        if (isset($result['data'])) {
            foreach ($result['data'] as $field => $value) {
                $this->$field = $value;
            }
        } elseif (isset($result['errors']) && $result['errors']) {
            throw new $classNameException('Error operation', $result['status_code'], $result['errors']);
        } else {
            throw new $classNameException('Unknown exception from in API', $result['status_code']);
        }

        return $this;
    }
}
