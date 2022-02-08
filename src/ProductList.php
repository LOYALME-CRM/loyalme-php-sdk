<?php

namespace LoyalmeCRM\LoyalmePhpSdk;

use LoyalmeCRM\LoyalmePhpSdk\Interfaces\ClientInterface;
use LoyalmeCRM\LoyalmePhpSdk\Interfaces\ProductInterface;
use LoyalmeCRM\LoyalmePhpSdk\Exceptions\ProductListException;
use LoyalmeCRM\LoyalmePhpSdk\Interfaces\ProductListInterface;
use LoyalmeCRM\LoyalmePhpSdk\Exceptions\ProductListSearchException;

/**
 * @property int|null $id
 * @property string|null $name
 * @property string|null $system_name
 * @property int|null $point_id
 */
class ProductList extends Api implements ProductListInterface
{
    const ACTION_LIST = 'product-list';
    const ACTION_CREATE = 'product-list';
    const ACTION_UPDATE = 'product-list/%d';
    const ACTION_ADD_PRODUCT = 'product-list/%d/add-product';
    const ACTION_REMOVE_PRODUCT = 'product-list/%d/remove-product';
    const ACTION_CONTENT = 'product-list/%d/content';

    /**
     * @return string
     */
    protected function _getClassNameException(): string
    {
        return ProductListException::class;
    }

    /**
     * @param string $name
     * @param string $systemName
     * @param int|null $pointId
     * @return ProductListInterface
     */
    protected function _create(
        string $name,
        string $systemName,
        int $pointId = null
    ): ProductListInterface
    {
        $url = self::ACTION_CREATE;
        $data = [
            'name' => $name,
            'system_name' => $systemName,
            'point_id' => $pointId
        ];
        $result = $this->_connection->sendPostRequest($url, $data);
        return $this->_fill($result);
    }

    /**
     * @throws Exceptions\LoyalmePhpSdkException
     */
    protected function _update(
        string $name,
        string $systemName,
        int $pointId = null
    ): ProductListInterface
    {
        $id = $this->findIdBySystemName($systemName);
        $url = sprintf(self::ACTION_UPDATE, $id);
        $result = $this->_connection->sendPutRequest($url, [
            'point_id' => $pointId,
            'name' => $name
        ]);
        return $this->_fill($result);
    }

    /**
     * @param string $systemName
     * @return int
     * @throws Exceptions\LoyalmePhpSdkException
     * @throws ProductListSearchException
     */
    protected function findIdBySystemName(string $systemName): int
    {
        $url = self::ACTION_LIST;
        $result = $this->_connection->sendGetRequest($url, ['system_name' => $systemName]);
        $this->checkResponseForErrors($result);
        if (!isset($result['data'][0]['id'])) {
            throw new ProductListSearchException(
                sprintf('Product list with systemName:[%s] was not found', $systemName),
                404
            );
        }
        return (int) $result['data'][0]['id'];
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

        $products = array_map(function ($value) {
            if (!$value instanceof ProductInterface) {
                throw new ProductListException('Product data must be an array of objects of the Product class', 422);
            }
            if (!isset($value->attributes['id'])) {
                throw new ProductListException('Before transferring data, you need to get or create the required product using the get () method', 422);
            }
            return [
                'quantity' => $value->quantity ?? 1,
                'product_id' => $value->id,
                'price_per_item' => $value->price,
            ];
        }, $products);

        $result = [];
        foreach ($products as $product) {
            $result[$product['product_id']] = $product;
        }

        return $result;
    }

    /**
     * @param string|null $systemName
     * @param string|null $name
     * @param int|null $pointId
     * @return ProductListInterface
     * @throws Exceptions\LoyalmePhpSdkException
     */
    public function get(
        string $systemName = null,
        string $name = null,
        int $pointId = null
    ): ProductListInterface
    {
        if ($systemName === null) {
            throw new ProductListException('Parameter [systemName] is required.');
        }
        try {
            $this->_update($name, $systemName, $pointId);
        } catch (ProductListSearchException $e) {
            $this->_create($name, $systemName, $pointId);
        }
        return $this;
    }

    /**
     * @param ProductListInterface $productList
     * @param array $products
     * @param ClientInterface|null $client
     * @param ProductInterface|null $relatedProduct
     * @return array
     * @throws ProductListException
     */
    public function updateContent(ProductListInterface $productList, array $products, ?ClientInterface $client = null, ?ProductInterface $relatedProduct = null): array
    {
        $products = $this->_processProductsArray($products);

        $urlContent = sprintf(self::ACTION_CONTENT, $productList->id);
        $content = $this->_connection->sendGetRequest($urlContent, [
            'related_product_id' => $relatedProduct->id ?? null,
            'client_id' => $client->id ?? null,
        ]);

        if (!isset($content['data'])) {
            throw new ProductListException(sprintf('Error getting content of product list [%s]', $productList->system_name), 0, $content);
        }

        $existingProducts = [];
        foreach ($content['data'] as $product) {
            $existingProducts[$product['product_id']] = $product;
        }

        $productsForAdding = [];
        $productsForDeleting = [];
        foreach ($products as $productId => $product) {
            if (isset($existingProducts[$productId])) {
                $existingProduct = $existingProducts[$productId];
                if ($existingProduct['price_per_item'] != $product['price_per_item'] || $existingProduct['quantity'] != $product['quantity']) {
                    $productsForDeleting[] = ['product_id' => $productId];
                    $productsForAdding[] = $product;
                } else {
                    unset($products[$productId]);
                }
            } else {
                $productsForAdding[] = $product;
            }
        }

        foreach ($existingProducts as $existingProductId => $existingProduct) {
            if (isset($products[$existingProductId])) {
                continue;
            }

            $productsForDeleting[] = ['product_id' => $existingProductId];
        }

        if ($productsForDeleting) {
            $urlRemovingProduct = sprintf(self::ACTION_REMOVE_PRODUCT, $productList->id);
            $resultOfDeleteing = $this->_connection->sendDeleteRequest($urlRemovingProduct, [
                'products' => $productsForDeleting,
                'related_product_id' => $relatedProduct->id ?? null,
                'client_id' => $client->id ?? null,
            ]);
            if (!isset($resultOfDeleteing['data']['id'])) {
                throw new ProductListException(sprintf('Unable to remove products from product list [%s]', $productList->system_name), 0, $resultOfDeleteing);
            }
        }

        if ($productsForAdding) {
            $urlAddingProduct = sprintf(self::ACTION_ADD_PRODUCT, $productList->id);
            $resultOfAdding = $this->_connection->sendPutRequest($urlAddingProduct, [
                'products' => $productsForAdding,
                'related_product_id' => $relatedProduct->id ?? null,
                'client_id' => $client->id ?? null,
            ]);
            if (isset($resultOfAdding['data']['id'])) {
                $content = $this->_connection->sendGetRequest($urlContent, [
                    'related_product_id' => $relatedProduct->id ?? null,
                    'client_id' => $client->id ?? null,
                ]);
                if (!isset($content['data'])) {
                    throw new ProductListException(sprintf('Error getting content of product list [%s]', $productList->system_name), 0, $content);
                }
            } else {
                throw new ProductListException(sprintf('Unable to adding products in product list [%s]', $productList->system_name), 0, $resultOfAdding);
            }
        }

        return $content['data'];
    }

    /**
     * @param ProductListInterface $productList
     * @param ClientInterface|null $client
     * @param ProductInterface|null $relatedProduct
     * @return array
     * @throws ProductListException
     */
    public function clear(ProductListInterface $productList, ?ClientInterface $client = null, ?ProductInterface $relatedProduct = null): bool
    {
        $urlRemovingProduct = sprintf(self::ACTION_REMOVE_PRODUCT, $productList->id);
        $resultOfDeleteing = $this->_connection->sendDeleteRequest($urlRemovingProduct, [
            'products' => null,
            'related_product_id' => $relatedProduct->id ?? null,
            'client_id' => $client->id ?? null,
        ]);

        if (!isset($resultOfDeleteing['data']['id'])) {
            throw new ProductListException(sprintf('Unable to remove products from product list [%s]', $productList->system_name), 0, $resultOfDeleteing);
        }

        $urlContent = sprintf(self::ACTION_CONTENT, $productList->id);
        $content = $this->_connection->sendGetRequest($urlContent, [
            'related_product_id' => $relatedProduct->id ?? null,
            'client_id' => $client->id ?? null,
        ]);
        if (!isset($content['data'])) {
            throw new ProductListException(sprintf('Error getting content of product list [%s]', $productList->system_name), 0, $content);
        }

        return empty($content['data']);
    }
}
