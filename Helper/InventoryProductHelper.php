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

    protected function addMandatoryAttributes($products): void
    {
        /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $products */
        $products->addAttributeToSelect('special_price')
            ->addAttributeToSelect('special_from_date')
            ->addAttributeToSelect('special_to_date')
            ->addAttributeToSelect('visibility')
            ->addAttributeToSelect('status');
    }

    public function productIsInStock($product, $storeId): bool
    {
        // Handled in ProductHelperPlugin
        return true;
    }
}
