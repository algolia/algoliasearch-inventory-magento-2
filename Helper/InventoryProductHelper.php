<?php

namespace Algolia\AlgoliaSearchInventory\Helper;

use Algolia\AlgoliaSearch\Api\Product\ReplicaManagerInterface;
use Algolia\AlgoliaSearch\Helper\ConfigHelper;
use Algolia\AlgoliaSearch\Helper\Entity\ProductHelper;
use Algolia\AlgoliaSearch\Logger\DiagnosticsLogger;
use Algolia\AlgoliaSearch\Service\AlgoliaConnector;
use Algolia\AlgoliaSearch\Service\IndexNameFetcher;
use Algolia\AlgoliaSearch\Service\IndexOptionsBuilder;
use Algolia\AlgoliaSearch\Service\IndexSettingsHandler;
use Algolia\AlgoliaSearch\Service\Product\FacetBuilder;
use Algolia\AlgoliaSearch\Service\Product\RecordBuilder as ProductRecordBuilder;
use Magento\Catalog\Api\Data\ProductInterfaceFactory;
use Magento\Catalog\Model\Product\Type;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\CatalogInventory\Helper\Stock;
use Magento\Eav\Model\Config;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\InventoryCatalog\Model\ResourceModel\AddStockDataToCollection;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Preference class to modify protected methods
 */
class InventoryProductHelper extends ProductHelper
{
    public function __construct(
        protected AddStockDataToCollection $addStockDataToCollection,
        protected StockHelper              $localStockHelper,
        Config                             $eavConfig,
        ConfigHelper                       $configHelper,
        AlgoliaConnector                   $algoliaConnector,
        IndexOptionsBuilder                $indexOptionsBuilder,
        DiagnosticsLogger                  $logger,
        StoreManagerInterface              $storeManager,
        ManagerInterface                   $eventManager,
        Visibility                         $visibility,
        Stock                              $deprecatedStockHelper,
        Type                               $productType,
        CollectionFactory                  $productCollectionFactory,
        IndexNameFetcher                   $indexNameFetcher,
        ReplicaManagerInterface            $replicaManager,
        ProductInterfaceFactory            $productFactory,
        ProductRecordBuilder               $productRecordBuilder,
        FacetBuilder                       $facetBuilder,
        IndexSettingsHandler               $indexSettingsHandler,
    ) {
        parent::__construct(
            $eavConfig,
            $configHelper,
            $algoliaConnector,
            $indexOptionsBuilder,
            $logger,
            $storeManager,
            $eventManager,
            $visibility,
            $deprecatedStockHelper,
            $productType,
            $productCollectionFactory,
            $indexNameFetcher,
            $replicaManager,
            $productFactory,
            $productRecordBuilder,
            $facetBuilder,
            $indexSettingsHandler
        );
    }

    /**
     * Explicitly apply stock filter from Magento_Inventory module
     */
    protected function addStockFilter($products, $storeId): void
    {
        try {
            $this->addStockDataToCollection->execute(
                $products,
                !$this->configHelper->getShowOutOfStock($storeId),
                $this->localStockHelper->getStockId($storeId)
            );
        } catch (LocalizedException $e) {
            $this->logger->error("Error applying MSI stock filter:" . $e->getMessage());
        }
    }
}
