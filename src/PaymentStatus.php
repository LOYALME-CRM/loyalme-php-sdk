<?php

namespace LoyalmeCRM\LoyalmePhpSdk;

use LoyalmeCRM\LoyalmePhpSdk\Exceptions\PaymentStatusException;
use LoyalmeCRM\LoyalmePhpSdk\Interfaces\PaymentStatusInterface;

class PaymentStatus extends Api implements PaymentStatusInterface
{
    const STATUS_NOT_ACTIVE = 0;
    const STATUS_ACTIVE = 1;

    const LIST_OF_PAYMENT_STATUSES = "payment-status";
    const CREATE_PAYMENT_STATUS = "payment-status";
    const SHOW_PAYMENT_STATUS = "payment-status/%d";
    const UPDATE_PAYMENT_STATUS = "payment-status/%d";
    const DELETE_PAYMENT_STATUS = "payment-status/%d";

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
     * @return PaymentStatusInterface
     * @throws Exceptions\LoyalmePhpSdkException
     */
    public function get(string $titleEn, string $slug, string $titleRu = '', int $isActive = self::STATUS_ACTIVE): PaymentStatusInterface
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
        $url = self::LIST_OF_PAYMENT_STATUSES;
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
     * @return PaymentStatus
     */
    protected function create(): PaymentStatus
    {
        $url = self::CREATE_PAYMENT_STATUS;
        $result = $this->_connection->sendPostRequest($url, $this->paramsArray);
        $this->fill($result);
        return $this;
    }

    /**
     * @return PaymentStatus
     */
    protected function update(): PaymentStatus
    {
        $url = sprintf(self::UPDATE_PAYMENT_STATUS, $this->lastSearchResult['data'][0]['id']);
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
     * @return PaymentStatus
     * @throws Exceptions\LoyalmePhpSdkException
     * @throws PaymentStatusException
     */
    public function delete(string $slug): PaymentStatus
    {
        $this->findBySlug($slug);
        if (empty($this->lastSearchResult['data'][0])) {
            throw new PaymentStatusException('Entity not found', 404);
        }
        $url = sprintf(self::DELETE_PAYMENT_STATUS, $this->lastSearchResult['data'][0]['id']);
        $result = $this->_connection->sendDeleteRequest($url);
        $this->fill($result);
        Log::printData($result, "Удаление");
        Log::printData($this, "Объект this");
        return $this;
    }

    /**
     * @return string
     */
    protected function getClassNameException(): string
    {
        return PaymentStatusException::class;
    }
}
