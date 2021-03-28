<?php

namespace LoyalmeCRM\LoyalmePhpSdk;

use LoyalmeCRM\LoyalmePhpSdk\Connection;
use LoyalmeCRM\LoyalmePhpSdk\Exceptions\LoyalmePhpSdk;

class Api
{
    const CLIENT_GENDER_NOT_SELECTED = 0;
    const CLIENT_GENDER_MALE = 1;
    const CLIENT_GENDER_FEMALE = 2;

    protected $_connection;

    public function __construct(Connection $connection)
    {
        $this->_connection = $connection;
    }

    public function validateArrayStructure(array $requiredFields, array $array, string $arrayKey, $isItem = true): void
    {
        if ($isItem) {
            foreach ($requiredFields as $field) {
                if (!isset($array[$field])) {
                    throw new LoyalmePhpSdk(sprintf('Attribute %s is required for array %s', $field, $arrayKey), 400, $array);
                }
            }
        } else {
            foreach ($array as $item) {
                foreach ($requiredFields as $field) {
                    if (!isset($item[$field])) {
                        throw new LoyalmePhpSdk(sprintf('Attribute %s is required for array %s', $field, $arrayKey), 400, $array);
                    }
                }
            }
        }
    }

    public function fill(array $result)
    {
        $classNameException = $this->getClassNameException();
        if (isset($result['data'])) {
            foreach ($result['data'] as $field => $value) {
                $this->$field = $value;
            }
        } elseif (isset($result['errors']) && $result['errors']) {
            throw new $classNameException('Error operation', $result['status_code'], $result['errors']);
        } else {
            throw new $classNameException('Unknow exception from in API', $result['status_code']);
        }

        return $this;
    }
}
