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
     * @var CategoryInterface
     */
    protected $parentCategory;

    /**
     * Category constructor.
     * @param Connection $connection
     * @param CategoryInterface|null $parentCategory
     * @throws CategoryException
     */
    public function __construct(Connection $connection, ?CategoryInterface $parentCategory = null)
    {
        parent::__construct($connection);
        $this->setParentCategory($parentCategory);
    }

    /**
     * @param CategoryInterface|null $parentCategory
     * @return CategoryInterface
     */
    public function setParentCategory(CategoryInterface $parentCategory = null): CategoryInterface
    {
        $this->parentCategory = isset($parentCategory) ? $parentCategory : null;
        return $this;
    }

    /**
     * @param string $extCategoryId
     * @param string $name
     * @return CategoryInterface
     */
    public function get(string $extCategoryId, string $name, ?CategoryInterface $parentCategory = null): CategoryInterface
    {
        $this->setParentCategory($parentCategory);
        try {
            $this->update($extCategoryId, $name);
        } catch (CategorySearchException $e) {
            $this->create($extCategoryId, $name, $this->parentCategory->external_id);
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

        $data = $this->fillParams($extCategoryId, $name, $this->parentCategory->id);
        $result = $this->_connection->sendPutRequest($url, $data);
        return $this->fill($result);
    }

    /**
     * @param string $extCategoryId
     * @return int
     * @throws CategorySearchException
     * @throws Exceptions\LoyalmePhpSdkException
     */
    protected function findByExtItemId(string $extCategoryId): int
    {
        $url = self::LIST_OF_CATEGORIES;
        $result = $this->_connection->sendGetRequest($url);
        $this->checkResponseForErrors($result);
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
     * @param int $parentCategoryId
     * @return array
     */
    protected function fillParams(string $extCategoryId, string $name, ?int $parentCategoryId): array
    {
        return [
            'name' => $name,
            'parent_id' => $parentCategoryId,
            'external_id' => $extCategoryId,
        ];
    }

    /**
     * @param string $extCategoryId
     * @param string $name
     * @param CategoryInterface|null $parentCategory
     * @return CategoryInterface
     */
    private function create(string $extCategoryId, string $name, ?CategoryInterface $parentCategory = null): CategoryInterface
    {
        $url = self::CREATE_CATEGORY;
        $parentId = $this->getParentCategoryId($parentCategory);
        $data = $this->fillParams($extCategoryId, $name, $parentId);
        $result = $this->_connection->sendPostRequest($url, $data);
        $this->fill($result);
        return $this;
    }

    /**
     * @param CategoryInterface|null $parentCategory
     * @return int|null
     */
    protected function getParentCategoryId(?CategoryInterface $parentCategory = null): ?int
    {
        if (isset($parentCategory)) {
            return $parentCategory->id;
        } else if (isset($this->parentCategory)) {
            return $this->parentCategory->id;
        }
        $id = isset($this->parentCategory) ? $this->parentCategory->id : null;
        return $id;
    }

    /**
     * @param string $extCategoryId
     * @return Category
     * @throws CategorySearchException
     * @throws Exceptions\LoyalmePhpSdkException
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
