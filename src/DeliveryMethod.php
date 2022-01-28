<?php

namespace LoyalmeCRM\LoyalmePhpSdk;

use LoyalmeCRM\LoyalmePhpSdk\Exceptions\DeliveryMethodException;
use LoyalmeCRM\LoyalmePhpSdk\Exceptions\DeliveryMethodSearchException;
use LoyalmeCRM\LoyalmePhpSdk\Interfaces\DeliveryMethodInterface;

class DeliveryMethod extends Api implements DeliveryMethodInterface
{
    const LIST_OF_PAYMENT_METHOD = 'delivery-method';
    const CREATE_PAYMENT_METHOD = 'delivery-method';
    const SHOW_PAYMENT_METHOD = 'delivery-method/%d';
    const UPDATE_PAYMENT_METHOD = 'delivery-method/%d';

    /**
     * DeliveryMethod constructor.
     * @param Connection $connection
     * @throws DeliveryMethodException
     */
    public function __construct(Connection $connection)
    {
        parent::__construct($connection);
    }

    /**
     * @param string $slug
     * @param string $titleEn
     * @param string $titleRu
     * @return DeliveryMethodInterface
     */
    public function get(string $slug, string $titleEn, string $titleRu = ''): DeliveryMethodInterface
    {
        try {
            $this->_update($slug, $titleEn, $titleRu);
        } catch (DeliveryMethodSearchException $e) {
            $this->_create($slug, $titleEn, $titleRu);
        }

        return $this;
    }

    /**
     * @param string $slug
     * @param string $titleEn
     * @param string $titleRu
     * @return DeliveryMethodInterface
     * @throws DeliveryMethodSearchException
     */
    protected function _update(string $slug, string $titleEn, string $titleRu = ''): DeliveryMethodInterface
    {
        $id = $this->_findBySlug($slug);
        $url = sprintf(self::UPDATE_PAYMENT_METHOD, $id);

        $data = $this->_fillParams($slug, $titleEn, $titleRu);
        $result = $this->_connection->sendPutRequest($url, $data);
        return $this->_fill($result);
    }

    /**
     * @param string $slug
     * @param string $titleEn
     * @param string $titleRu
     * @return DeliveryMethodInterface
     * @throws DeliveryMethodSearchException
     */
    protected function _findBySlug(string $slug): int
    {
        $url = self::LIST_OF_PAYMENT_METHOD;
        $result = $this->_connection->sendGetRequest($url, ['slug' => $slug]);
        $this->checkResponseForErrors($result);
        if (!isset($result['data'][0]['id'])) {
            throw new DeliveryMethodSearchException(sprintf('Delivery method status slug:[%s] was not found', $slug), 404);
        }
        $deliveryMethodId = (int) $result['data'][0]['id'];

        return $deliveryMethodId;
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
     * @return DeliveryMethodInterface
     */
    protected function _create(string $slug, string $titleEn, string $titleRu = ''): DeliveryMethodInterface
    {
        $url = self::CREATE_PAYMENT_METHOD;
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
        return DeliveryMethodSearchException::class;
    }
}
