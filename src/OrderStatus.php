<?php

namespace LoyalmeCRM\LoyalmePhpSdk;

use LoyalmeCRM\LoyalmePhpSdk\Exceptions\OrderStatusException;
use LoyalmeCRM\LoyalmePhpSdk\Interfaces\OrderStatusInterface;

class OrderStatus extends Api implements OrderStatusInterface
{
    const STATUS_NOT_ACTIVE = 0;
    const STATUS_ACTIVE = 1;

    const LIST_OF_ORDER_STATUSES = "order-status";
    const CREATE_ORDER_STATUS = "order-status";
    const SHOW_ORDER_STATUS = "order-status/%d";
    const UPDATE_ORDER_STATUS = "order-status/%d";
    const DELETE_ORDER_STATUS = "order-status/%d";

    /**
     * @var array
     */
    protected $lastSearchResult;

    /**
     * @var array
     */
    protected $paramsArray;

    /**
     * @var Connection
     */
    protected $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
        parent::__construct($connection);
    }

    /**
     * @param string $titleEn
     * @param string $slug
     * @param string $titleRu
     * @param int $isActive
     * @return OrderStatusInterface
     * @throws Exceptions\LoyalmePhpSdkException
     */
    public function get(string $titleEn, string $slug, string $titleRu = '', int $isActive = self::STATUS_ACTIVE): OrderStatusInterface
    {
        $this->findBySlug($slug);
        $this->fillParams($titleEn, $slug, $titleRu, $isActive);
        if (empty($this->lastSearchResult['data'][0])) {
            $this->create();
        } else {
            $this->update();
        }
        return $this;
    }

    /**
     * @param string $slug
     * @throws Exceptions\LoyalmePhpSdkException
     */
    protected function findBySlug(string $slug)
    {
        $url = self::LIST_OF_ORDER_STATUSES;
        $this->lastSearchResult = $this->connection->sendGetRequest($url, ['slug' => $slug]);
        $this->checkResponseForErrors($this->lastSearchResult);
        if (empty($this->lastSearchResult['data'])) {
            return false;
        } else {
            return $this->lastSearchResult['data'][0];
        }
    }

    /**
     * @param string $titleEn
     * @param string $slug
     * @param string $titleRu
     * @param int $isActive
     * @return array
     */
    protected function fillParams(string $titleEn, string $slug, string $titleRu = '', int $isActive = self::STATUS_ACTIVE): array
    {
        $this->paramsArray = [
            'title_en' => $titleEn,
            'slug' => $slug,
            'title_ru' => $titleRu,
            'is_active' => $isActive,
        ];
        return $this->paramsArray;
    }

    /**
     * @return OrderStatusInterface
     */
    protected function create(): OrderStatusInterface
    {
        $url = self::CREATE_ORDER_STATUS;
        $result = $this->_connection->sendPostRequest($url, $this->paramsArray);
        $this->fill($result);
        return $this;
    }

    /**
     * @return OrderStatus
     */
    protected function update(): OrderStatus
    {
        $url = sprintf(self::UPDATE_ORDER_STATUS, $this->lastSearchResult['data'][0]['id']);
        $result = $this->_connection->sendPutRequest($url, $this->getOnlyFilledParams());
        $this->fill($result);
        return $this;
    }

    protected function getOnlyFilledParams()
    {
        return array_filter($this->paramsArray, function ($item) {
            return !empty($item);
        });
    }

    /**
     * @param string $slug
     * @return OrderStatusInterface
     * @throws Exceptions\LoyalmePhpSdkException
     * @throws OrderStatusException
     */
    public function delete(string $slug): OrderStatusInterface
    {
        $this->findBySlug($slug);
        if (empty($this->lastSearchResult['data'][0])) {
            throw new OrderStatusException('Entity not found', 404);
        }
        $url = sprintf(self::DELETE_ORDER_STATUS, $this->lastSearchResult['data'][0]['id']);
        $result = $this->_connection->sendDeleteRequest($url, []);
        $this->fill($result);
        return $this;
    }

    /**
     * @return string
     */
    protected function getClassNameException(): string
    {
        return OrderStatusException::class;
    }
}
