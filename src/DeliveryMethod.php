<?php

namespace LoyalmeCRM\LoyalmePhpSdk;

use LoyalmeCRM\LoyalmePhpSdk\Exceptions\DeliveryMethodException;
use LoyalmeCRM\LoyalmePhpSdk\Interfaces\DeliveryMethodInterface;

class DeliveryMethod extends Api implements DeliveryMethodInterface
{
    const STATUS_NOT_ACTIVE = 0;
    const STATUS_ACTIVE = 1;

    const LIST_OF_DELIVERY_METHODS = "delivery-method";
    const CREATE_DELIVERY_METHOD = "delivery-method";
    const SHOW_DELIVERY_METHOD = "delivery-method/%d";
    const UPDATE_DELIVERY_METHOD = "delivery-method/%d";
    const DELETE_DELIVERY_METHOD = "delivery-method/%d";

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
     * @return DeliveryMethodInterface
     * @throws Exceptions\LoyalmePhpSdkException
     */
    public function get(string $titleEn, string $slug, string $titleRu = '', int $isActive = self::STATUS_ACTIVE): DeliveryMethodInterface
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
     * @return bool|mixed
     * @throws Exceptions\LoyalmePhpSdkException
     */
    protected function findBySlug(string $slug)
    {
        $url = self::LIST_OF_DELIVERY_METHODS;
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
     * @return DeliveryMethodInterface
     */
    protected function create(): DeliveryMethodInterface
    {
        $url = self::CREATE_DELIVERY_METHOD;
        $result = $this->_connection->sendPostRequest($url, $this->paramsArray);
        $this->fill($result);
        return $this;
    }

    /**
     * @return DeliveryMethod
     */
    protected function update(): DeliveryMethod
    {
        $url = sprintf(self::UPDATE_DELIVERY_METHOD, $this->lastSearchResult['data'][0]['id']);
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
     * @return DeliveryMethodInterface
     * @throws Exceptions\LoyalmePhpSdkException
     */
    public function delete(string $slug): DeliveryMethodInterface
    {
        $this->findBySlug($slug);
        if (empty($this->lastSearchResult['data'][0])) {
            throw new DeliveryMethodException('Entity not found', 404);
        }
        $url = sprintf(self::DELETE_DELIVERY_METHOD, $this->lastSearchResult['data'][0]['id']);
        $result = $this->_connection->sendDeleteRequest($url, []);
        $this->fill($result);
        return $this;
    }

    /**
     * @return string
     */
    protected function getClassNameException(): string
    {
        return DeliveryMethodException::class;
    }
}
