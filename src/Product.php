<?php

namespace LoyalmeCRM\LoyalmePhpSdk;

use LoyalmeCRM\LoyalmePhpSdk\Exceptions\ProductException;
use LoyalmeCRM\LoyalmePhpSdk\Interfaces\ProductInterface;

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
     * @var array
     */
    private $categories;

    /**
     * Product constructor.
     * @param Connection $connection
     * @param array $categories
     */
    public function __construct(Connection $connection, array $categories)
    {
        parent::__construct($connection);
        $this->categories = $categories;
    }

    /**
     * @param string $extCategoryId
     * @param string $name
     * @return CategoryInterface|null
     * @throws CategoryException
     */
    public function get(string $extCategoryId, string $name): CategoryInterface
    {
        $id = $this->findByExtCategoryId($extCategoryId);
        $result = $this->update($id, $extCategoryId, $name, $this->parentCategory);
        return $result;
    }

    /**
     * @param string $extCategoryId
     * @return int
     * @throws CategoryException
     */
    protected function findByExtItemId(string $extCategoryId): int
    {
        $url = self::LIST_OF_CATEGORIES;
        $result = $this->_connection->sendGetRequest($url);
        $search = array_values(array_filter($result['data'], function ($innerArray) use ($extCategoryId) {
            return $innerArray['external_id'] == $extCategoryId;
        }));
        if (!isset($search[0]['id'])) {
            throw new CategoryException('Category was not found', 404);
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

    /**
     * @return string
     */
    protected function getClassNameException(): string
    {
        return ProductException::class;
    }
}