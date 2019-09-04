<?php

namespace Algolia\AlgoliaSearchInventory\Plugin\InventoryApi;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Indexer\IndexerRegistry;
use Magento\InventoryApi\Api\Data\SourceItemInterface;
use Magento\InventoryCatalogApi\Model\GetProductIdsBySkusInterface;

abstract class AbstractSourceItemsPlugin
{
    /** @var GetProductIdsBySkusInterface */
    private $getProductIdsBySkus;

    /** @var IndexerRegistry */
    private $indexer;

    public function __construct(
        GetProductIdsBySkusInterface $getProductIdsBySkus,
        IndexerRegistry $indexerRegistry
    ) {
        $this->getProductIdsBySkus = $getProductIdsBySkus;
        $this->indexer = $indexerRegistry->get('algolia_products');
    }

    /**
     * @param SourceItemInterface[] $sourceItems$sourceItems
     */
    protected function reindexFromSourceItems($sourceItems)
    {
        $productIds = [];

        foreach ($sourceItems as $sourceItem) {
            $sku = $sourceItem->getSku();
            try {
                $productId = (int)$this->getProductIdsBySkus->execute([$sku])[$sku];
                $productIds[] = $productId;
            } catch (NoSuchEntityException $e) {
                continue;
            }
        }

        $productIds = array_unique($productIds);
        if (!$this->indexer->isScheduled()) {
            $this->indexer->reindexList($productIds);
        }
    }
}
