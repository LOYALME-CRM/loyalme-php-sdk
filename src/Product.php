<?php

namespace LoyalmeCRM\LoyalmePhpSdk;

use LoyalmeCRM\LoyalmePhpSdk\Exceptions\ProductException;
use LoyalmeCRM\LoyalmePhpSdk\Exceptions\ProductSearchException;
use LoyalmeCRM\LoyalmePhpSdk\Interfaces\CategoryInterface;
use LoyalmeCRM\LoyalmePhpSdk\Interfaces\ProductInterface;

class Product extends Api implements ProductInterface
{
    const LIST_OF_PRODUCTS = 'product';
    const CREATE_PRODUCT = 'product';
    const UPDATE_PRODUCT = 'product/%d';
    const DELETE_PRODUCT = 'product/%d';

    const PRODUCT_STATUS_NOT_ACTIVE = 0;
    const PRODUCT_STATUS_ACTIVE = 1;

    const PRODUCT_TYPE_PRODUCT = 1;
    const PRODUCT_TYPE_SAMPLE = 2;
    const PRODUCT_TYPE_GIFT = 3;

    /**
     * Product constructor.
     * @param Connection $connection
     * @param array $categories
     */
    public function __construct(Connection $connection)
    {
        parent::__construct($connection);
    }

    /**
     * @param int|null $extItemId
     * @param string|null $barcode
     * @param string|null $title
     * @param float|null $price
     * @param string|null $photo
     * @param int $isActive
     * @param int $typeId
     * @param float $accrualRate
     * @param array $categories
     * @param array $aliases
     * @param array $customFields
     * @return ProductInterface
     * @throws ProductException
     */
    public function get(
        ?int $extItemId = null,
        ?string $barcode = null,
        ?string $title = null,
        ?float $price = null,
        ?string $photo = null,
        int $isActive = 1,
        int $typeId = 1,
        float $accrualRate = 1,
        array $categories = [],
        array $aliases = [],
        array $customFields = []
    ): ProductInterface
    {
        if (empty($extItemId) && empty($barcode)) {
            throw new ProductException('Parameters [extItemId] or [barcode] must be filled.');
        }
        $categoriesArray = $this->processCategoriesArray($categories);
        try {
            $id = $this->findByExtItemIdOrBarcode($extItemId, $barcode);
            $result = $this->_update($id, $title, $price, $photo, $extItemId, $barcode, $isActive, $typeId, $accrualRate, $categoriesArray, $aliases, $customFields);
            return $result;
        } catch (ProductSearchException $e) {
            if (empty($title) || empty($price)) {
                throw new ProductException('Parameters [title] and [price] must be both filled. It\'s required fields!');
            }
            $result = $this->_create($title, $price, $photo, $extItemId, $barcode, $isActive, $typeId, $accrualRate, $categoriesArray, $aliases, $customFields);
            return $result;
        }
    }

    /**
     * @param array $array
     * @return array
     * @throws ProductException
     */
    private function processCategoriesArray(array $array = []): array
    {
        if (empty($array)) {
            throw new ProductException('The category parameter is required and must be filled', 422);
        }

        return array_map(function ($value) {
            if (!$value instanceof CategoryInterface) {
                throw new ProductException('Category data must be an array of objects of the Category class', 422);
            }
            if (!isset($value->attributes['id'])) {
                throw new ProductException('Before transferring data, you need to get or create the required category using the get () method', 422);
            }
            return $value->id;
        }, $array);
    }

    /**
     * @param int|null $extItemId
     * @param string|null $barcode
     * @return int
     * @throws ProductException
     * @throws ProductSearchException
     */
    protected function findByExtItemIdOrBarcode(int $extItemId = null, string $barcode = null): int
    {
        if (empty($extItemId) and empty($barcode)) {
            throw new ProductException('At least one of the parameters must be specified.');
        }
        $url = self::LIST_OF_PRODUCTS;
        $parameters = [];
        if ($extItemId) $parameters['ext_item_id'] = $extItemId;
        if ($barcode) $parameters['barcode'] = $barcode;

        $result = $this->_connection->sendGetRequest($url, $parameters);
        if (isset($result['status_code']) and $result['status_code'] != 200) {
            $messageIfMessageAbsent = sprintf('Message is absent, result is: %s', print_r($result, true));
            $message = isset($result['message']) ? $result['message'] : $messageIfMessageAbsent;
            throw new ProductException($message, $result['status_code']);
        } else {
            if ($result['meta']['pagination']['total'] === 0) {
                throw new ProductSearchException('Product was not found by external ID', 404);
            }
        }
        if (isset($result['data'][0]['id'])) {
            return $result['data'][0]['id'];
        } else {
            throw new  ProductSearchException('Ошибка при поиске через API: ', $result['status_code']);
        }
    }

    /**
     * @param int $id
     * @param string $title
     * @param float|null $price
     * @param string|null $photo
     * @param int|null $extItemId
     * @param string|null $barcode
     * @param int $isActive
     * @param int $typeId
     * @param float $accrualRate
     * @param array $categories
     * @param array $aliases
     * @param array $customFields
     * @return ProductInterface
     */
    protected function _update(
        int $id,
        string $title,
        ?float $price = null,
        ?string $photo = null,
        ?int $extItemId = null,
        ?string $barcode = null,
        int $isActive = 1,
        int $typeId = 1,
        float $accrualRate = 1,
        array $categories = [],
        array $aliases = [],
        array $customFields = []
    ): ProductInterface
    {
        $url = sprintf(self::UPDATE_PRODUCT, $id);
        $data = $this->fillParams(
            $title,
            $price,
            $photo,
            $extItemId,
            $barcode,
            $isActive,
            $typeId,
            $accrualRate,
            $categories,
            $aliases,
            $customFields);
        $result = $this->_connection->sendPutRequest($url, $data);
        return $this->_fill($result);
    }

    /**
     * @param string $title
     * @param float|null $price
     * @param string|null $photo
     * @param int|null $extItemId
     * @param string|null $barcode
     * @param int $isActive
     * @param int $typeId
     * @param float $accrualRate
     * @param array $categories
     * @param array $aliases
     * @param array $customFields
     * @return array
     */
    private function fillParams(
        string $title,
        ?float $price = null,
        ?string $photo = null,
        ?int $extItemId = null,
        ?string $barcode = null,
        int $isActive = 1,
        int $typeId = 1,
        float $accrualRate = 1,
        array $categories = [],
        array $aliases = [],
        array $customFields = []
    ): array
    {
        $parametersArray = [
            'title' => $title,
            'is_active' => $isActive,
            'type_id' => $typeId,
            'accrual_rate' => $accrualRate,
            'categories' => $categories,
            'aliases' => $aliases,
        ];
        if (!is_null($price)) {
            $parametersArray['price'] = $price;
        }
        if (!is_null($photo)) {
            $parametersArray['ext_photo_url'] = $photo;
        }
        if (!is_null($extItemId)) {
            $parametersArray['ext_item_id'] = $extItemId;
        }
        if (!is_null($barcode)) {
            $parametersArray['barcode'] = $barcode;
        }

        foreach ($customFields as $key => $value) {
            $parametersArray[$key] = $value;
        }
        return $parametersArray;
    }

    /**
     * @param string $title
     * @param float $price
     * @param string|null $photo
     * @param int|null $extItemId
     * @param string|null $barcode
     * @param int $isActive
     * @param int $typeId
     * @param float $accrualRate
     * @param array $categories
     * @param array $aliases
     * @param array $customFields
     * @return ProductInterface
     */
    protected function _create(
        string $title,
        float $price,
        ?string $photo = null,
        ?int $extItemId = null,
        ?string $barcode = null,
        int $isActive = 1,
        int $typeId = 1,
        float $accrualRate = 1,
        array $categories = [],
        array $aliases = [],
        array $customFields = []
    ): ProductInterface
    {
        $url = self::CREATE_PRODUCT;
        $data = $this->fillParams($title, $price, $photo, $extItemId, $barcode, $isActive, $typeId, $accrualRate, $categories, $aliases, $customFields);
        $result = $this->_connection->sendPostRequest($url, $data);
        return $this->_fill($result);
    }

    /**
     * @param string|null $extItemId
     * @param string|null $barcode
     * @return ProductInterface
     * @throws ProductException
     * @throws ProductSearchException
     */
    private function delete(string $extItemId = null, string $barcode = null): ProductInterface
    {
        $id = $this->findByExtItemIdOrBarcode($extItemId, $barcode);
        $url = sprintf(self::DELETE_PRODUCT, $id);
        $result = $this->_connection->sendDeleteRequest($url);
        return $this->_fill($result);
    }

    /**
     * @return string
     */
    protected function _getClassNameException(): string
    {
        return ProductException::class;
    }
}
