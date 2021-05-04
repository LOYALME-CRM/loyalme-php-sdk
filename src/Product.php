<?php

namespace LoyalmeCRM\LoyalmePhpSdk;

use LoyalmeCRM\LoyalmePhpSdk\Exceptions\ProductException;
use LoyalmeCRM\LoyalmePhpSdk\Exceptions\ProductSearchException;
use LoyalmeCRM\LoyalmePhpSdk\Interfaces\ProductInterface;
use mysql_xdevapi\Exception;
use const src\API_URL;

class Product extends Api implements ProductInterface
{
    const LIST_OF_PRODUCTS = "product";
    const CREATE_PRODUCT = "product";
    const SHOW_PRODUCT = "product/%d";
    const UPDATE_PRODUCT = "product/%d";
    const DELETE_PRODUCT = "product/%d";

    const PRODUCT_STATUS_NOT_ACTIVE = 0;
    const PRODUCT_STATUS_ACTIVE = 1;

    const PRODUCT_TYPE_PRODUCT = 1;
    const PRODUCT_TYPE_SAMPLE = 2;
    const PRODUCT_TYPE_GIFT = 3;

    /**
     * @var array
     */
    protected $_categories;

    /**
     * Product constructor.
     * @param Connection $connection
     * @param array $categories
     */
    public function __construct(Connection $connection, array $categories)
    {
        parent::__construct($connection);
        $this->_categories = $categories;
    }

    /**
     * @param string $title
     * @param float $price
     * @param string $photo
     * @param string $extItemId
     * @param string $barcode
     * @param int $isActive
     * @param int $typeId
     * @param float $accrualRate
     * @param array $aliases
     * @param mixed ...$attribute
     * @return ProductInterface
     * @throws ProductSearchException
     */
    public function get(
        string $title,
        float $price,
        string $photo,
        string $extItemId = null,
        string $barcode = null,
        int $isActive = 1,
        int $typeId = 1,
        float $accrualRate = 1,
        array $aliases = []
    ): ProductInterface
    {
        $id = $this->findByExtItemIdOrBarcode($extItemId, $barcode);
        $result = $this->_connection->sendGetRequest(sprintf(self::SHOW_PRODUCT, $itemId));
        return $this->fill($result);
    }

    /**
     * @param string $extCategoryId
     * @return int
     * @throws CategoryException
     */
    protected function findByExtItemIdOrBarcode(string $extCategoryId = null, string $barcode = null): int
    {
        $url = self::LIST_OF_PRODUCTS;
        $result = $this->_connection->sendGetRequest($url);
        if (isset($result['status_code']) and $result['status_code']!=200){
            $messageIfMessageAbsent =  sprintf('Сообщение отсутствует, результат операции: %s',print_r($result,true));
            $message = isset($result['message']) ? $result['message'] : $messageIfMessageAbsent;
            Log::printData('Ошибка');
            throw new ProductException($message,$result['status_code']);
        }
        Log::printData($result, 'result of find:');
        $search = array_values(array_filter($result['data'], function ($innerArray) use ($extCategoryId) {
            return $innerArray['external_id'] == $extCategoryId;
        }));
        if (!isset($search[0]['id'])) {
            throw new ProductSearchException('Product was not found by external ID', 404);
        }
        $categoryId = (integer)$search[0]['id'];

        return $categoryId;
    }

    /**
     * @param string $extCategoryId
     * @param string $name
     * @param int|null $parentExtCategoryId
     * @return CategoryInterface
     */
    public function create(string $extCategoryId, string $name, int $parentExtCategoryId = null): CategoryInterface
    {
        $url = self::CREATE_CATEGORY;
        $data = $this->fillParams($extCategoryId, $name, $parentExtCategoryId);
        $result = $this->_connection->sendPostRequest($url, $data);
        $this->fill($result);
        return $this;
    }

    /**
     * @param string $extProductId
     * @return Product
     */
    public function delete(string $extProductId)
    {
        $id = $this->findByExtCategoryId($extCategoryId);
        $url = sprintf(self::DELETE_CATEGORY, $id);
        $result = $this->_connection->sendDeleteRequest($url);
        return $this->fill($result);
    }

    protected function findByItemBarcode(int $barcode): int
    {
        $url = self::LIST_OF_PRODUCTS;
        $result = $this->_connection->sendGetRequest($url);
        Log::printData($result, 'result of find:');
        $search = array_values(array_filter($result['data'], function ($innerArray) use ($extCategoryId) {
            return $innerArray['barcode'] == $barcode;
        }));
        if (!isset($search[0]['id'])) {
            throw new ProductSearchException('Product was not found by barcode', 404);
        }
        $categoryId = (integer)$search[0]['id'];

        return $categoryId;
    }

    /**
     * @param string $extCategoryId
     * @param string $name
     * @param int|null $parentExtCategoryId
     * @return CategoryInterface
     * @throws CategoryException
     */
    protected function update(string $extCategoryId, string $name, int $parentExtCategoryId = null): CategoryInterface
    {
        $id = $this->findByExtCategoryId($extCategoryId);
        $url = sprintf(self::UPDATE_CATEGORY, $id);
        $data = $this->fillParams($extCategoryId, $name, $parentExtCategoryId);
        $result = $this->_connection->sendPutRequest($url, $data);
        return $this->fill($result);
    }

    /**
     * @param string $extCategoryId
     * @param string $name
     * @param int|null $parentExtCategoryId
     * @return array
     */
    private function fillParams(string $extCategoryId, string $name, int $parentExtCategoryId = null): array
    {
        return [
            'name' => $name,
            'parent_id' => $parentExtCategoryId,
            'external_id' => $extCategoryId,
        ];
    }

    /**
     * @return string
     */
    protected function getClassNameException(): string
    {
        return ProductException::class;
    }
}