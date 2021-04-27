<?php

namespace LoyalmeCRM\LoyalmePhpSdk;

use LoyalmeCRM\LoyalmePhpSdk\Exceptions\CategoryException;
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
     * @param CategoryInterface|null $parentCategoryExtId
     */
    public function __construct(Connection $connection, CategoryInterface $parentCategoryExtId = null)
    {
        parent::__construct($connection);
        $this->parentCategoryExtId = $parentCategoryExtId;
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
    protected function findByExtCategoryId(string $extCategoryId): int
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
     * @param int $id
     * @param string $extCategoryId
     * @param string $name
     * @param int|null $parentExtCategoryId
     * @return CategoryInterface
     */
    public function delete(string $extCategoryId)
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
        return CategoryException::class;
    }
}