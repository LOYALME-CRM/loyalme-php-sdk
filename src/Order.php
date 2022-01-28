<?php

namespace LoyalmeCRM\LoyalmePhpSdk;

use LoyalmeCRM\LoyalmePhpSdk\Exceptions\OrderException;
use LoyalmeCRM\LoyalmePhpSdk\Interfaces\OrderInterface;
use LoyalmeCRM\LoyalmePhpSdk\Interfaces\ClientInterface;
use LoyalmeCRM\LoyalmePhpSdk\Interfaces\ProductInterface;
use LoyalmeCRM\LoyalmePhpSdk\Exceptions\OrderSearchException;
use LoyalmeCRM\LoyalmePhpSdk\Interfaces\OrderStatusInterface;
use LoyalmeCRM\LoyalmePhpSdk\Interfaces\PaymentMethodInterface;
use LoyalmeCRM\LoyalmePhpSdk\Interfaces\DeliveryMethodInterface;
use LoyalmeCRM\LoyalmePhpSdk\Interfaces\PaymentStatusInterface;

class Order extends Api implements OrderInterface
{
    const LIST_OF_ORDER = 'order';
    const CREATE_ORDER = 'order';
    const SHOW_ORDER = 'order/%d';
    const UPDATE_ORDER = 'order/%d';

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
     * @param string $externalId
     * @param ClientInterface $client
     * @param array $products
     * @param string|null $promoCode
     * @param string|null $orderLink
     * @param OrderStatusInterface|null $orderStatus
     * @param PaymentMethodInterface|null $paymentMethod
     * @param DeliveryMethodInterface|null $deliveryMethod
     * @param PaymentStatusInterface|null $paymentStatus
     * @return OrderInterface
     */
    public function get(
        string $externalId,
        ClientInterface $client,
        array $products,
        ?string $promoCode = null,
        ?string $orderLink = null,
        ?OrderStatusInterface $orderStatus = null,
        ?PaymentMethodInterface $paymentMethod = null,
        ?DeliveryMethodInterface $deliveryMethod = null,
        ?PaymentStatusInterface $paymentStatus = null
    ): OrderInterface {
        $products = $this->_processProductsArray($products);
        try {
            $this->_update($externalId, $client, $products, $promoCode, $orderLink, $orderStatus, $paymentMethod, $deliveryMethod, $paymentStatus);
        } catch (OrderSearchException $e) {
            $this->_create($externalId, $client, $products, $promoCode, $orderLink, $orderStatus, $paymentMethod, $deliveryMethod, $paymentStatus);
        }

        return $this;
    }

    /**
     * @param array $products
     * @return array
     * @throws OrderException
     */
    private function _processProductsArray(array $products = []): array
    {
        if (empty($products)) {
            throw new ProductException('The products parameter is required and must be filled', 422);
        }

        return array_map(function ($value) {
            if (!$value instanceof ProductInterface) {
                throw new OrderException('Product data must be an array of objects of the Product class', 422);
            }
            if (!isset($value->attributes['id'])) {
                throw new OrderException('Before transferring data, you need to get or create the required product using the get () method', 422);
            }
            return [
                'quantity' => $value->quantity ?? 1,
                'product_id' => $value->id,
                'price' => $value->price,
            ];
        }, $products);
    }

    /**
     * @param string $externalId
     * @param ClientInterface $client
     * @param array $products
     * @param string|null $promoCode
     * @param string|null $orderLink
     * @param OrderStatusInterface|null $orderStatus
     * @param PaymentMethodInterface|null $paymentMethod
     * @param DeliveryMethodInterface|null $deliveryMethod
     * @param PaymentStatusInterface|null $paymentStatus
     * @return OrderInterface
     * @throws OrderSearchException
     */
    protected function _update(
        string $externalId,
        ClientInterface $client,
        array $products,
        ?string $promoCode = null,
        ?string $orderLink = null,
        ?OrderStatusInterface $orderStatus = null,
        ?PaymentMethodInterface $paymentMethod = null,
        ?DeliveryMethodInterface $deliveryMethod = null,
        ?PaymentStatusInterface $paymentStatus = null
    ): OrderInterface {
        $id = $this->_findByExtOrderId($externalId);
        $url = sprintf(self::UPDATE_ORDER, $id);

        $data = $this->_fillParams($externalId, $client, $products, $promoCode, $orderLink, $orderStatus, $paymentMethod, $deliveryMethod, $paymentStatus);
        $result = $this->_connection->sendPutRequest($url, $data);
        return $this->_fill($result);
    }

    /**
     * @param string $externalId
     * @return int
     * @throws OrderSearchException
     */
    protected function _findByExtOrderId(string $externalId): int
    {
        $url = self::LIST_OF_ORDER;
        $result = $this->_connection->sendGetRequest($url, ['ext_order_id' => $externalId]);
        $this->checkResponseForErrors($result);
        if (!isset($result['data'][0]['id'])) {
            throw new OrderSearchException(sprintf('Order status slug:[%s] was not found', $externalId), 404);
        }
        $orderId = (int) $result['data'][0]['id'];

        return $orderId;
    }

    /**
     * @param string $externalId
     * @param ClientInterface $client
     * @param array $products
     * @param string|null $promoCode
     * @param string|null $orderLink
     * @param OrderStatusInterface|null $orderStatus
     * @param PaymentMethodInterface|null $paymentMethod
     * @param DeliveryMethodInterface|null $deliveryMethod
     * @param PaymentStatusInterface|null $paymentStatus
     * @return array
     */
    protected function _fillParams(
        string $externalId,
        ClientInterface $client,
        array $products,
        ?string $promoCode = null,
        ?string $orderLink = null,
        ?OrderStatusInterface $orderStatus = null,
        ?PaymentMethodInterface $paymentMethod = null,
        ?DeliveryMethodInterface $deliveryMethod = null,
        ?PaymentStatusInterface $paymentStatus = null
    ): array {
        $result = [
            'person_id' => $this->_connection->personId,
            'point_id' => $this->_connection->pointId,
            'client_id' => $client->id,
            'ext_order_id' => $externalId,
            'products' => $products,
        ];

        if (!is_null($promoCode)) {
            $result['promo_code'] = $promoCode;
        }
        if (!is_null($orderLink)) {
            $result['order_link'] = $orderLink;
        }
        if (!is_null($orderStatus)) {
            $result['status'] = $orderStatus->slug ?? null;
        }
        if (!is_null($paymentMethod)) {
            $result['payment_type'] = $paymentMethod->slug ?? null;
        }
        if (!is_null($deliveryMethod)) {
            $result['shipping_type1'] = $deliveryMethod->slug ?? null;
        }
        if (!is_null($paymentStatus)) {
            $result['payment_status_id'] = $paymentStatus->id ?? null;
        }

        return $result;
    }

    /**
     * @param string $externalId
     * @param ClientInterface $client
     * @param array $products
     * @param string|null $promoCode
     * @param string|null $orderLink
     * @param OrderStatusInterface|null $orderStatus
     * @param PaymentMethodInterface|null $paymentMethod
     * @param DeliveryMethodInterface|null $deliveryMethod
     * @param PaymentStatusInterface|null $paymentStatus
     * @return OrderInterface
     */
    protected function _create(
        string $externalId,
        ClientInterface $client,
        array $products,
        ?string $promoCode = null,
        ?string $orderLink = null,
        ?OrderStatusInterface $orderStatus = null,
        ?PaymentMethodInterface $paymentMethod = null,
        ?DeliveryMethodInterface $deliveryMethod = null,
        ?PaymentStatusInterface $paymentStatus = null
    ): OrderInterface {
        $url = self::CREATE_ORDER;
        $data = $this->_fillParams($externalId, $client, $products, $promoCode, $orderLink, $orderStatus, $paymentMethod, $deliveryMethod, $paymentStatus);
        $result = $this->_connection->sendPostRequest($url, $data);
        $this->_fill($result);

        return $this;
    }

    /**
     * @return string
     */
    protected function _getClassNameException(): string
    {
        return OrderException::class;
    }
}
