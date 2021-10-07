<?php

namespace LoyalmeCRM\LoyalmePhpSdk;

use LoyalmeCRM\LoyalmePhpSdk\Exceptions\ProductListException;
use LoyalmeCRM\LoyalmePhpSdk\Exceptions\ProductListSearchException;
use LoyalmeCRM\LoyalmePhpSdk\Exceptions\ProductSearchException;
use LoyalmeCRM\LoyalmePhpSdk\Interfaces\ProductListInterface;

/**
 * @property int|null $id
 * @property string|null $name
 * @property string|null $system_name
 * @property int|null $point_id
 */
class ProductList extends Api implements ProductListInterface
{
    const LIST_OF_PRODUCT_LIST = 'product-list';
    const CREATE_PRODUCT_LIST = 'product-list';
    const UPDATE_PRODUCT_LIST = 'product-list/%d';
    const ADD_PRODUCT_TO_PRODUCT_LIST = 'product-list/%d/add-product';


    /**
     * @return string
     */
    protected function getClassNameException(): string
    {
        return ProductListException::class;
    }

    /**
     * @param string|null $systemName
     * @param string|null $name
     * @param int|null $pointId
     * @return ProductListInterface
     * @throws Exceptions\LoyalmePhpSdkException
     */
    public function get(
        string $systemName = null,
        string $name = null,
        int $pointId = null
    ): ProductListInterface
    {
        if ($systemName === null) {
            throw new ProductListException('Parameter [systemName] is required.');
        }
        try {
            $this->update($name, $systemName, $pointId);
        } catch (ProductListSearchException $e) {
            $this->create($name, $systemName, $pointId);
        }
        return $this;
    }

    /**
     * @param string $name
     * @param string $systemName
     * @param int $pointId
     * @return ProductListInterface
     */
    protected function create(
        string $name,
        string $systemName,
        int $pointId
    ): ProductListInterface
    {
        $url = self::CREATE_PRODUCT_LIST;
        $data = [
            'name' => $name,
            'system_name' => $systemName,
            'point_id' => $pointId
        ];
        $result = $this->_connection->sendPutRequest($url, $data);
        return $this->fill($result);
    }

    /**
     * @throws Exceptions\LoyalmePhpSdkException
     */
    protected function update(
        string $name,
        string $systemName,
        int $pointId = null
    ): ProductListInterface
    {
        $id = $this->findIdBySystemName($systemName);
        $url = sprintf(self::UPDATE_PRODUCT_LIST, $id);
        $result = $this->_connection->sendPutRequest($url, [
            'point_id' => $pointId,
            'name' => $name
        ]);
        return $this->fill($result);
    }

    /**
     * @param string $systemName
     * @return int
     * @throws Exceptions\LoyalmePhpSdkException
     * @throws ProductListSearchException
     */
    protected function findIdBySystemName(string $systemName): int
    {
        $url = self::LIST_OF_PRODUCT_LIST;
        $result = $this->_connection->sendGetRequest($url, ['system_name' => $systemName]);
        $this->checkResponseForErrors($result);
        if (!isset($result['data'][0]['id'])) {
            throw new ProductListSearchException(
                sprintf('Product list with systemName:[%s] was not found', $systemName),
                404
            );
        }
        return (int) $result['data'][0]['id'];
    }
}
