<?php

namespace Algolia\AlgoliaSearchInventory\Helper;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\InventorySalesApi\Api\Data\SalesChannelInterface;
use Magento\InventorySalesApi\Api\StockResolverInterface;
use Magento\Store\Model\StoreManagerInterface;

class StockHelper
{
    public function __construct(
        protected StoreManagerInterface $storeManager,
        protected StockResolverInterface $stockResolver
    ) { }

    /**
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function getStockId(int $storeId): int
    {
        $websiteId = $this->storeManager->getStore($storeId)->getWebsiteId();
        return (int) $this->stockResolver->execute(
            SalesChannelInterface::TYPE_WEBSITE,
            $this->storeManager->getWebsite($websiteId)->getCode()
        )->getStockId();
    }
}
