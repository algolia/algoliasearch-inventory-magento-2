<?php

namespace Algolia\AlgoliaSearchInventory\Plugin\Helper;

use Algolia\AlgoliaSearch\Helper\Entity\ProductHelper;
use Magento\Catalog\Model\Product;
use Magento\Framework\App\ResourceConnection;
use Magento\InventoryIndexer\Model\StockIndexTableNameResolverInterface;
use Magento\InventorySalesApi\Api\Data\SalesChannelInterface;
use Magento\InventorySalesApi\Api\StockResolverInterface;
use Magento\Store\Model\StoreManagerInterface;

class ProductHelperPlugin
{
    /** @var StoreManagerInterface */
    private $storeManager;

    /** @var StockResolverInterface */
    private $stockResolver;

    /** @var StockIndexTableNameResolverInterface */
    private $stockIndexTableNameResolver;

    /** @var ResourceConnection */
    private $resourceConnection;

    public function __construct(
        StoreManagerInterface $storeManager,
        StockResolverInterface $stockResolver,
        StockIndexTableNameResolverInterface $stockIndexTableNameResolver,
        ResourceConnection $resourceConnection
    ) {
        $this->storeManager = $storeManager;
        $this->stockResolver = $stockResolver;
        $this->stockIndexTableNameResolver = $stockIndexTableNameResolver;
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * @param ProductHelper $subject
     * @param int $result
     * @param Product $product
     * @param int $storeId
     *
     * @return int
     *
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function afterProductIsInStock(
        ProductHelper $subject,
        $result,
        $product,
        $storeId
    ) {
        $connection = $this->resourceConnection->getConnection();
        $select = $connection->select()
            ->from(
                ['stock_index' => $this->getStockTableName($storeId)],
                ['is_salable']
            )
            ->where('stock_index.sku = ?', $product->getSku());

        return (int) $connection->fetchOne($select);
    }

    /**
     * int $storeId
     *
     * @return string
     *
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getStockTableName($storeId)
    {
        $tableName = $this->stockIndexTableNameResolver->execute($this->getStockId($storeId));
        return $this->resourceConnection->getTableName($tableName);
    }

    /**
     * @param int $storeId
     *
     * @return int
     *
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getStockId($storeId)
    {
        $websiteId = $this->storeManager->getStore($storeId)->getWebsiteId();
        return (int)$this->stockResolver->execute(
            SalesChannelInterface::TYPE_WEBSITE,
            $this->storeManager->getWebsite($websiteId)->getCode()
        )->getStockId();
    }
}
