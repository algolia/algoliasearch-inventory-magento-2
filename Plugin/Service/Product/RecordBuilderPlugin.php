<?php

namespace Algolia\AlgoliaSearchInventory\Plugin\Service\Product;

use Algolia\AlgoliaSearch\Service\Product\RecordBuilder;
use Magento\Catalog\Model\Product;
use Algolia\AlgoliaSearchInventory\Service\Product\InventoryProductRecordBuilder;

/**
 * Plugin class to modify public methods
 */
class RecordBuilderPlugin
{
    public function afterProductIsInStock(
        RecordBuilder $builder,
        bool          $result,
        Product       $product,
        int           $storeId
    ): bool
    {
        return $product->isSalable();
    }

    public function afterAddInStock(
        RecordBuilder $builder,
                      $result,
                      $defaultData,
                      $customData,
        Product       $product
    ): mixed
    {
        if (!isset($defaultData[InventoryProductRecordBuilder::ATTR_IN_STOCK])) {
            $result[InventoryProductRecordBuilder::ATTR_IN_STOCK] = $builder->productIsInStock($product, $product->getStoreId());
        }

        return $result;
    }
}
