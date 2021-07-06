<?php

use Codeception\Test\Unit;
use LoyalmeCRM\LoyalmePhpSdk\Category;
use LoyalmeCRM\LoyalmePhpSdk\Connection;

class CategoryTest extends Unit
{
    /**
     * @var UnitTester
     */
    protected $tester;

    /**
     * @var Connection
     */
    protected $_connection;

    protected function _before()
    {
        $token = $this->tester->getConfig('token');
        $apiUrl = $this->tester->getConfig('apiUrl');
        $brandId = $this->tester->getConfig('brandId');
        $pointId = $this->tester->getConfig('pointId');
        $personId = $this->tester->getConfig('personId');
        $this->_connection = new Connection($token, $apiUrl, $brandId, $pointId, $personId);
    }

    protected function _after()
    {
        
    }

    public function testConnectionObject()
    {
        $this->assertTrue($this->_connection instanceof Connection);
    }

    public function testCreatingCategory()
    {
        $extId = md5(date('Ymdhms') . rand(1000, 9999));
        $nameOfCategory = 'category' . $extId;
        $categoryObject = new Category($this->_connection);
        $category = $categoryObject->get($extId, $nameOfCategory);

        $this->assertTrue($category instanceof Category);
        $this->assertEquals($nameOfCategory, $category->name);
        $this->assertEmpty($category->parent_id);
        $this->assertEquals($extId, $category->external_id);
    }

    public function testCreatingCategoryWithParentCategory()
    {
        $extId1 = md5(date('Ymdhms') . rand(1000, 9999));
        $nameOfCategory1 = 'category' . $extId1;
        $categoryObject = new Category($this->_connection);
        $category1 = $categoryObject->get($extId1, $nameOfCategory1);

        $this->assertTrue($category1 instanceof Category);
        $this->assertEquals($nameOfCategory1, $category1->name);
        $this->assertEmpty($category1->parent_id);
        $this->assertEquals($extId1, $category1->external_id);

        $extId2 = md5(date('sYmdhm') . rand(1000, 9999));
        $nameOfCategory2 = 'category' . $extId2;
        $categoryObject2 = new Category($this->_connection);
        $category2 = $categoryObject2->get($extId2, $nameOfCategory2, $category1);

        $this->assertTrue($category2 instanceof Category);
        $this->assertEquals($nameOfCategory2, $category2->name);
        $this->assertEquals($category1->id, $category2->parent_id);
        $this->assertEquals($extId1, $category1->external_id);
    }
}
