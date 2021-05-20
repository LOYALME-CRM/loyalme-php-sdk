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
                if (!isset($array[$field])) {
                    throw new LoyalmePhpSdkException(sprintf('Attribute %s is required for array %s', $field, $arrayKey), 400, $array);
                }
            }
        } else {
            foreach ($array as $item) {
                foreach ($requiredFields as $field) {
                    if (!isset($item[$field])) {
                        throw new LoyalmePhpSdkException(sprintf('Attribute %s is required for array %s', $field, $arrayKey), 400, $array);
                    }
                }
            }
        }
    }

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
        }elseif ($result['status_code']==200){
            $this->attributes['result']=$result;
        }elseif (isset($result['errors']) && $result['errors']) {
            throw new $classNameException($result['message'], $result['status_code'], $result['errors']);
        } else {
            $details = is_array($result) ? json_encode($result) : (string) $result;
            $errorMessage = sprintf('Another exception from API. Details: %s',$details);
            throw new $classNameException($errorMessage, $result['status_code']);
        }

        return $this;
    }
}
