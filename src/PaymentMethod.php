<?php

namespace LoyalmeCRM\LoyalmePhpSdk;

use LoyalmeCRM\LoyalmePhpSdk\Exceptions\PaymentMethodException;
use LoyalmeCRM\LoyalmePhpSdk\Exceptions\PaymentMethodSearchException;
use LoyalmeCRM\LoyalmePhpSdk\Interfaces\PaymentMethodInterface;

class PaymentMethod extends Api implements PaymentMethodInterface
{
    const LIST_OF_PAYMENT_METHOD = 'payment-method';
    const CREATE_PAYMENT_METHOD = 'payment-method';
    const SHOW_PAYMENT_METHOD = 'payment-method/%d';
    const UPDATE_PAYMENT_METHOD = 'payment-method/%d';

    /**
     * PaymentMethod constructor.
     * @param Connection $connection
     * @throws PaymentMethodException
     */
    public function __construct(Connection $connection)
    {
        parent::__construct($connection);
    }

    /**
     * @param string $slug
     * @param string $titleEn
     * @param string $titleRu
     * @return PaymentMethodInterface
     */
    public function get(string $slug, string $titleEn, string $titleRu = ''): PaymentMethodInterface
    {
        if (empty($titleRu)) {
            $titleRu = $titleEn;
        }

        try {
            $this->_update($slug, $titleEn, $titleRu);
        } catch (PaymentMethodSearchException $e) {
            $this->_create($slug, $titleEn, $titleRu);
        }

        return $this;
    }

    /**
     * @param string $slug
     * @param string $titleEn
     * @param string $titleRu
     * @return PaymentMethodInterface
     * @throws PaymentMethodSearchException
     */
    protected function _update(string $slug, string $titleEn, string $titleRu = ''): PaymentMethodInterface
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
     * @return PaymentMethodInterface
     * @throws PaymentMethodSearchException
     */
    protected function _findBySlug(string $slug): int
    {
        $url = self::LIST_OF_PAYMENT_METHOD;
        $result = $this->_connection->sendGetRequest($url);
        $this->checkResponseForErrors($result);
        $search = array_values(array_filter($result['data'], function ($innerArray) use ($slug) {
            return $innerArray['slug'] == $slug;
        }));
        if (!isset($search[0]['id'])) {
            throw new PaymentMethodSearchException(sprintf('Payment method status slug:[%s] was not found', $slug), 404);
        }
        $paymentMethodId = (int) $search[0]['id'];

        return $paymentMethodId;
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
     * @return PaymentMethodInterface
     */
    protected function _create(string $slug, string $titleEn, string $titleRu = ''): PaymentMethodInterface
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
        return PaymentMethodSearchException::class;
    }
}
