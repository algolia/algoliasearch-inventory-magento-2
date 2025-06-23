<?php

namespace Algolia\AlgoliaSearchInventory\Plugin\Helper;

use Algolia\AlgoliaSearch\Helper\Entity\ProductHelper;
use Algolia\AlgoliaSearchInventory\Helper\StockHelper;
use Magento\Catalog\Model\Product;
use Magento\Framework\App\ResourceConnection;
use Magento\InventoryIndexer\Model\StockIndexTableNameResolverInterface;

class ProductHelperPlugin
{
    public function __construct(
        protected StockHelper $stockHelper,
        protected StockIndexTableNameResolverInterface $stockIndexTableNameResolver,
        protected ResourceConnection $resourceConnection
    ) { }

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
    protected function getStockTableName(int $storeId): string
    {
        $tableName = $this->stockIndexTableNameResolver->execute($this->stockHelper->getStockId($storeId));
        return $this->resourceConnection->getTableName($tableName);
    }
}
