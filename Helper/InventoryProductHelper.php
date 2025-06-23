<?php

namespace Algolia\AlgoliaSearchInventory\Helper;

use Algolia\AlgoliaSearch\Api\Product\ReplicaManagerInterface;
use Algolia\AlgoliaSearch\Helper\AlgoliaHelper;
use Algolia\AlgoliaSearch\Helper\ConfigHelper;
use Algolia\AlgoliaSearch\Helper\Entity\ProductHelper;
use Algolia\AlgoliaSearch\Logger\DiagnosticsLogger;
use Algolia\AlgoliaSearch\Service\IndexNameFetcher;
use Algolia\AlgoliaSearch\Service\Product\RecordBuilder as ProductRecordBuilder;
use Magento\Catalog\Api\Data\ProductInterfaceFactory;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Type;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\CatalogInventory\Helper\Stock;
use Magento\Customer\Api\GroupExcludedWebsiteRepositoryInterface;
use Magento\Customer\Model\ResourceModel\Group\Collection as GroupCollection;
use Magento\Directory\Model\Currency as CurrencyHelper;
use Magento\Eav\Model\Config;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\InventoryCatalog\Model\ResourceModel\AddStockDataToCollection;
use Magento\Store\Model\StoreManagerInterface;

class InventoryProductHelper extends ProductHelper
{
    public function __construct(
        protected AddStockDataToCollection      $addStockDataToCollection,
        protected StockHelper                   $localStockHelper,
        Config                                  $eavConfig,
        ConfigHelper                            $configHelper,
        AlgoliaHelper                           $algoliaHelper,
        DiagnosticsLogger                       $logger,
        StoreManagerInterface                   $storeManager,
        ManagerInterface                        $eventManager,
        Visibility                              $visibility,
        Stock                                   $deprecatedStockHelper,
        CurrencyHelper                          $currencyManager,
        Type                                    $productType,
        CollectionFactory                       $productCollectionFactory,
        GroupCollection                         $groupCollection,
        GroupExcludedWebsiteRepositoryInterface $groupExcludedWebsiteRepository,
        IndexNameFetcher                        $indexNameFetcher,
        ReplicaManagerInterface                 $replicaManager,
        ProductInterfaceFactory                 $productFactory,
        ProductRecordBuilder                    $productRecordBuilder,
    ) {
        parent::__construct(
            $eavConfig,
            $configHelper,
            $algoliaHelper,
            $logger,
            $storeManager,
            $eventManager,
            $visibility,
            $deprecatedStockHelper,
            $currencyManager,
            $productType,
            $productCollectionFactory,
            $groupCollection,
            $groupExcludedWebsiteRepository,
            $indexNameFetcher,
            $replicaManager,
            $productFactory,
            $productRecordBuilder
        );
    }

    protected function addInStock($defaultData, $customData, Product $product)
    {
        if (isset($defaultData['in_stock']) === false) {
            $customData['in_stock'] = $this->productIsInStock($product, $product->getStoreId());
        }

        return $customData;
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

    public function productIsInStock($product, $storeId): bool
    {
        // Handled in ProductHelperPlugin
        return true;
    }
}
