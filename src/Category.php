<?php

namespace LoyalmeCRM\LoyalmePhpSdk;

use LoyalmeCRM\LoyalmePhpSdk\Interfaces\CategoryInterface;

class Category implements CategoryInterface
{
    /**
     * @var Connection
     */
    protected $_connection;
    /**
     * @var CategoryInterface|null
     */
    protected $_parentCategory;

    /**
     * Category constructor.
     * @param Connection $connection
     * @param CategoryInterface|null $parentCategory
     */
    public function __construct(Connection $connection, CategoryInterface $parentCategory = null)
    {
        $this->_connection = $connection;
        $this->_parentCategory = $parentCategory;
    }

    /**
     * @param string $extCategoryId
     * @param string $name
     * @return CategoryInterface
     */
    public function get(string $extCategoryId, string $name): CategoryInterface
    {

    }

    /**
     * @param string $extCategoryId
     * @param string $name
     * @param int|null $parentExtCategoryId
     * @return CategoryInterface
     */
    protected function create(string $extCategoryId, string $name, int $parentExtCategoryId = null): CategoryInterface
    {

    }

    /**
     * @param int $id
     * @param string $extCategoryId
     * @param string $name
     * @param int|null $parentExtCategoryId
     * @return CategoryInterface
     */
    protected function update(int $id, string $extCategoryId, string $name, int $parentExtCategoryId = null): CategoryInterface
    {

    }

    /**
     * @param string $extCategoryId
     * @return CategoryInterface
     */
    protected function findByExtCategoryId(string $extCategoryId): CategoryInterface
    {

    }
}