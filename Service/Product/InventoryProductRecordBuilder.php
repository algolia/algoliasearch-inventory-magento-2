<?php

namespace Algolia\AlgoliaSearchInventory\Service\Product;

use Algolia\AlgoliaSearch\Helper\AlgoliaHelper;
use Algolia\AlgoliaSearch\Helper\ConfigHelper;
use Algolia\AlgoliaSearch\Helper\Entity\CategoryHelper;
use Algolia\AlgoliaSearch\Helper\Entity\Product\PriceManager;
use Algolia\AlgoliaSearch\Helper\Image as ImageHelper;
use Algolia\AlgoliaSearch\Logger\DiagnosticsLogger;
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

class InventoryProductRecordBuilder extends RecordBuilder
{
    const STOCK_QTY_ATTR = 'stock_qty';

    public function __construct(
        protected GetProductSalableQtyInterface $salableQty,
        protected StockHelper                   $stockHelper,
        ManagerInterface                        $eventManager,
        DiagnosticsLogger                       $logger,
        Visibility                              $visibility,
        StoreManagerInterface                   $storeManager,
        ConfigHelper                            $configHelper,
        CategoryHelper                          $categoryHelper,
        AlgoliaHelper                           $algoliaHelper,
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
            $algoliaHelper,
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
        if (!isset($defaultData[self::STOCK_QTY_ATTR])
            && $this->isAttributeEnabled($additionalAttributes, self::STOCK_QTY_ATTR)) {
            $customData[self::STOCK_QTY_ATTR] =
                (int) $this->salableQty->execute(
                    $product->getSku(),
                    $this->stockHelper->getStockId($product->getStoreId())
                );
        }

        return $customData;
    }
}
