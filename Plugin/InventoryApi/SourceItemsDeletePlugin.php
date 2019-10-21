<?php

namespace Algolia\AlgoliaSearchInventory\Plugin\InventoryApi;

use Magento\InventoryApi\Api\Data\SourceItemInterface;
use Magento\InventoryApi\Api\SourceItemsDeleteInterface;

class SourceItemsDeletePlugin extends AbstractSourceItemsPlugin
{
    /**
     * @param SourceItemsDeleteInterface $subject
     * @param void $result
     * @param SourceItemInterface[] $sourceItems
     */
    public function afterExecute(
        SourceItemsDeleteInterface $subject,
        $result,
        array $sourceItems
    ) {
        $this->reindexFromSourceItems($sourceItems);
    }
}
