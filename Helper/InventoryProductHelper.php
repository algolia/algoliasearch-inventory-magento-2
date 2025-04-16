<?php

namespace Algolia\AlgoliaSearchInventory\Helper;

use Algolia\AlgoliaSearch\Helper\Entity\ProductHelper;
use Magento\Catalog\Model\Product;

class InventoryProductHelper extends ProductHelper
{
    protected function addInStock($defaultData, $customData, Product $product)
    {
        if (isset($defaultData['in_stock']) === false) {
            $customData['in_stock'] = $this->productIsInStock($product, $product->getStoreId());
        }

        return $customData;
    }

    /**
     * This method is overriden and left empty to remove the native stock filter behaviour
     * (from CatalogInventory Helper)
     *
     * @param $products
     * @param $storeId
     */
    protected function addStockFilter($products, $storeId): void
    {
        //void
    }

    public function productIsInStock($product, $storeId): bool
    {
        // Handled in ProductHelperPlugin
        return true;
    }
}
