<?php

namespace Algolia\AlgoliaSearchInventory\Service\Product;

use Algolia\AlgoliaSearch\Helper\ConfigHelper;
use Algolia\AlgoliaSearch\Helper\Entity\CategoryHelper;
use Algolia\AlgoliaSearch\Helper\Entity\Product\PriceManager;
use Algolia\AlgoliaSearch\Helper\Image as ImageHelper;
use Algolia\AlgoliaSearch\Logger\DiagnosticsLogger;
use Algolia\AlgoliaSearch\Service\AlgoliaConnector;
use Algolia\AlgoliaSearch\Service\Category\RecordBuilder as CategoryRecordBuilder;
use Algolia\AlgoliaSearch\Service\Product\RecordBuilder;
use Algolia\AlgoliaSearchInventory\Helper\StockHelper;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Visibility;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\InventorySalesApi\Api\GetProductSalableQtyInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Preference class to modify protected methods
 */
class InventoryProductRecordBuilder extends RecordBuilder
{
    const ATTR_STOCK_QTY = 'stock_qty';
    const ATTR_IN_STOCK = 'in_stock';

    public function __construct(
        protected GetProductSalableQtyInterface $salableQty,
        protected StockHelper                   $stockHelper,
        ManagerInterface                        $eventManager,
        DiagnosticsLogger                       $logger,
        Visibility                              $visibility,
        StoreManagerInterface                   $storeManager,
        ConfigHelper                            $configHelper,
        CategoryHelper                          $categoryHelper,
        CategoryRecordBuilder                   $categoryRecordBuilder,
        AlgoliaConnector                        $algoliaConnector,
        ImageHelper                             $imageHelper,
        StockRegistryInterface                  $stockRegistry,
        PriceManager                            $priceManager
    ){
        parent::__construct(
            $eventManager,
            $logger,
            $visibility,
            $storeManager,
            $configHelper,
            $categoryHelper,
            $categoryRecordBuilder,
            $algoliaConnector,
            $imageHelper,
            $stockRegistry,
            $priceManager
        );
    }

    /**
     * Retrieve qty from MSI by stock ID
     * @throws NoSuchEntityException
     * @throws InputException
     * @throws LocalizedException
     */
    protected function addStockQty($defaultData, $customData, $additionalAttributes, Product $product) {
        if (!isset($defaultData[self::ATTR_STOCK_QTY])
            && $this->isAttributeEnabled($additionalAttributes, self::ATTR_STOCK_QTY)) {
            $customData[self::ATTR_STOCK_QTY] = !!$customData[self::ATTR_IN_STOCK]
                 ? (int) $this->salableQty->execute(
                        $product->getSku(),
                        $this->stockHelper->getStockId($product->getStoreId())
                    )
                : 0 // Avoids error: Can't check requested quantity for products without Source Items support.
            ;
        }

        return $customData;
    }
}
