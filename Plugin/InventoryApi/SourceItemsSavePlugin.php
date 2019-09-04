<?php

namespace Algolia\AlgoliaSearchInventory\Plugin\InventoryApi;

use Magento\InventoryApi\Api\Data\SourceItemInterface;
use Magento\InventoryApi\Api\SourceItemsSaveInterface;

class SourceItemsSavePlugin extends AbstractSourceItemsPlugin
{
    /**
     * @param SourceItemsSaveInterface $subject
     * @param void $result
     * @param SourceItemInterface[] $sourceItems
     */
    public function afterExecute(
        SourceItemsSaveInterface $subject,
        $result,
        array $sourceItems
    ) {
        $this->reindexFromSourceItems($sourceItems);
    }
}
