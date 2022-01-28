<?php

namespace LoyalmeCRM\LoyalmePhpSdk;

use LoyalmeCRM\LoyalmePhpSdk\Exceptions\OrderStatusException;
use LoyalmeCRM\LoyalmePhpSdk\Exceptions\OrderStatusSearchException;
use LoyalmeCRM\LoyalmePhpSdk\Interfaces\OrderStatusInterface;

class OrderStatus extends Api implements OrderStatusInterface
{
    const LIST_OF_ORDER_STATUS = 'order-status';
    const CREATE_ORDER_STATUS = 'order-status';
    const SHOW_ORDER_STATUS = 'order-status/%d';
    const UPDATE_ORDER_STATUS = 'order-status/%d';

    /**
     * OrderStatus constructor.
     * @param Connection $connection
     * @throws OrderStatusException
     */
    public function __construct(Connection $connection)
    {
        parent::__construct($connection);
    }

    /**
     * @param string $slug
     * @param string $titleEn
     * @param string $titleRu
     * @return OrderStatusInterface
     */
    public function get(string $slug, string $titleEn, string $titleRu = ''): OrderStatusInterface
    {
        try {
            $this->_update($slug, $titleEn, $titleRu);
        } catch (OrderStatusSearchException $e) {
            $this->_create($slug, $titleEn, $titleRu);
        }

        return $this;
    }

    /**
     * @param string $slug
     * @param string $titleEn
     * @param string $titleRu
     * @return OrderStatusInterface
     * @throws OrderStatusSearchException
     */
    protected function _update(string $slug, string $titleEn, string $titleRu = ''): OrderStatusInterface
    {
        $id = $this->_findBySlug($slug);
        $url = sprintf(self::UPDATE_ORDER_STATUS, $id);

        $data = $this->_fillParams($slug, $titleEn, $titleRu);
        $result = $this->_connection->sendPutRequest($url, $data);
        return $this->_fill($result);
    }

    /**
     * @param string $slug
     * @param string $titleEn
     * @param string $titleRu
     * @return OrderStatusInterface
     * @throws OrderStatusSearchException
     */
    protected function _findBySlug(string $slug): int
    {
        $url = self::LIST_OF_ORDER_STATUS;
        $result = $this->_connection->sendGetRequest($url, ['slug' => $slug]);
        $this->checkResponseForErrors($result);
        if (!isset($result['data'][0]['id'])) {
            throw new OrderStatusSearchException(sprintf('Order status slug:[%s] was not found', $slug), 404);
        }
        $orderStatusId = (int) $result['data'][0]['id'];

        return $orderStatusId;
    }

    /**
     * @param string $slug
     * @param string $titleEn
     * @param string $titleRu
     * @return array
     */
    protected function _fillParams(string $slug, string $titleEn, string $titleRu = ''): array
    {
        return [
            'slug' => $slug,
            'title_en' => $titleEn,
            'title_ru' => $titleRu,
            'is_active' => 1,
        ];
    }

    /**
     * @param string $slug
     * @param string $titleEn
     * @param string $titleRu
     * @return OrderStatusInterface
     */
    protected function _create(string $slug, string $titleEn, string $titleRu = ''): OrderStatusInterface
    {
        $url = self::CREATE_ORDER_STATUS;
        $data = $this->_fillParams($slug, $titleEn, $titleRu);
        $result = $this->_connection->sendPostRequest($url, $data);
        $this->_fill($result);

        return $this;
    }

    /**
     * @return string
     */
    protected function _getClassNameException(): string
    {
        return OrderStatusSearchException::class;
    }
}
