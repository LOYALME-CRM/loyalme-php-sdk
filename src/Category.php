<?php

namespace LoyalmeCRM\LoyalmePhpSdk;

use LoyalmeCRM\LoyalmePhpSdk\Exceptions\CategoryException;
use LoyalmeCRM\LoyalmePhpSdk\Exceptions\CategorySearchException;
use LoyalmeCRM\LoyalmePhpSdk\Interfaces\CategoryInterface;

class Category extends Api implements CategoryInterface
{
    const LIST_OF_CATEGORIES = "category";
    const CREATE_CATEGORY = "category";
    const SHOW_CATEGORY = "category/%d";
    const UPDATE_CATEGORY = "category/%d";
    const DELETE_CATEGORY = "category/%d";

    /**
     * @var CategoryInterface|null
     */
    protected $parentCategoryExtId;

    /**
     * Category constructor.
     * @param Connection $connection
     * @param int|null $parentCategoryExtId
     * @throws CategoryException
     */
    public function __construct(Connection $connection, int $parentCategoryExtId = null)
    {
        parent::__construct($connection);
        $this->setParentCategory($parentCategoryExtId);
    }

    /**
     * @param null $parentCategoryExtId
     * @return CategoryInterface
     * @throws CategoryException
     */
    public function setParentCategory($parentCategoryExtId = null): CategoryInterface
    {
        if (isset($parentCategoryExtId)) {
            $this->findByExtItemId($parentCategoryExtId);
        }
        $this->parentCategoryExtId = $parentCategoryExtId;
        return $this;
    }

    /**
     * @param string $extCategoryId
     * @return int
     * @throws CategorySearchException
     */
    protected function findByExtItemId(string $extCategoryId): int
    {
        $url = self::LIST_OF_CATEGORIES;
        $result = $this->_connection->sendGetRequest($url);
        $search = array_values(array_filter($result['data'], function ($innerArray) use ($extCategoryId) {
            return $innerArray['external_id'] == $extCategoryId;
        }));
        if (!isset($search[0]['id'])) {
            throw new CategorySearchException(sprintf('Category extId:[%s] was not found', $extCategoryId), 404);
        }
        $categoryId = (integer)$search[0]['id'];

        return $categoryId;
    }

    /**
     * @param string $extCategoryId
     * @param string $name
     * @return CategoryInterface
     */
    public function get(string $extCategoryId, string $name): CategoryInterface
    {
        try {
            $this->update($extCategoryId, $name);
        } catch (CategorySearchException $e) {
            $this->create($extCategoryId, $name, $this->parentCategoryExtId);
        }
        return $this;
    }

    /**
     * @param string $extCategoryId
     * @param string $name
     * @return CategoryInterface
     * @throws CategorySearchException
     */
    protected function update(string $extCategoryId, string $name): CategoryInterface
    {
        $id = $this->findByExtItemId($extCategoryId);
        $url = sprintf(self::UPDATE_CATEGORY, $id);
        $data = $this->fillParams($extCategoryId, $name, $this->parentCategoryExtId);
        $result = $this->_connection->sendPutRequest($url, $data);
        return $this->fill($result);
    }

    /**
     * @param string $extCategoryId
     * @param string $name
     * @param int|null $parentExtCategoryId
     * @return array
     */
    protected function fillParams(string $extCategoryId, string $name, int $parentExtCategoryId = null): array
    {
        $parent_id = $parentExtCategoryId ?: $this->parentCategoryExtId;
        return [
            'name' => $name,
            'parent_id' => $parent_id,
            'external_id' => $extCategoryId,
        ];
    }

    /**
     * @param string $extCategoryId
     * @param string $name
     * @param int|null $parentExtCategoryId
     * @return CategoryInterface
     */
    private function create(string $extCategoryId, string $name, int $parentExtCategoryId = null): CategoryInterface
    {
        $url = self::CREATE_CATEGORY;
        $data = $this->fillParams($extCategoryId, $name, $parentExtCategoryId);
        $result = $this->_connection->sendPostRequest($url, $data);
        $this->fill($result);
        return $this;
    }

    /**
     * @param string $extCategoryId
     * @return Category
     * @throws CategorySearchException
     */
    public function delete(string $extCategoryId)
    {
        $id = $this->findByExtItemId($extCategoryId);
        $url = sprintf(self::DELETE_CATEGORY, $id);
        $result = $this->_connection->sendDeleteRequest($url);
        return $this->fill($result);
    }

    /**
     * @return string
     */
    protected function getClassNameException(): string
    {
        return CategoryException::class;
    }
}