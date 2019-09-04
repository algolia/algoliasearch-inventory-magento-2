<?php

namespace Algolia\AlgoliaSearchInventory\Helper;

use Algolia\AlgoliaSearch\Helper\Entity\ProductHelper;
use Magento\Catalog\Model\Product;

class InventoryProductHelper extends ProductHelper
{
    protected function addInStock($defaultData, $customData, Product $product)
    {
        if (isset($defaultData['in_stock']) === false) {
            // We don't rely on getStockItem anymore
            $customData['in_stock'] = 1;
        }

        return $customData;
    }

    protected function addStockFilter($products, $storeId)
    {
        return $products;
    }

    protected function addMandatoryAttributes($products)
    {
        /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $products */
        $products->addAttributeToSelect('special_price')
            ->addAttributeToSelect('special_from_date')
            ->addAttributeToSelect('special_to_date')
            ->addAttributeToSelect('visibility')
            ->addAttributeToSelect('status');

        return $products;
    }

    public function productIsInStock($product, $storeId)
    {
        // Handled in ProductHelperPlugin
        return true;
    }
}
