<?php

declare(strict_types=1);

namespace Algolia\AlgoliaSearchInventory\Test\Unit\Service\Product;

use Algolia\AlgoliaSearch\Helper\ConfigHelper;
use Algolia\AlgoliaSearch\Helper\Entity\CategoryHelper;
use Algolia\AlgoliaSearch\Helper\Entity\Product\PriceManager;
use Algolia\AlgoliaSearch\Helper\Image as ImageHelper;
use Algolia\AlgoliaSearch\Logger\DiagnosticsLogger;
use Algolia\AlgoliaSearch\Service\AlgoliaConnector;
use Algolia\AlgoliaSearch\Service\Category\RecordBuilder as CategoryRecordBuilder;
use Algolia\AlgoliaSearch\Test\TestCase;
use Algolia\AlgoliaSearchInventory\Helper\StockHelper;
use Algolia\AlgoliaSearchInventory\Service\Product\InventoryProductRecordBuilder;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Gallery\ReadHandler as GalleryReadHandler;
use Magento\Catalog\Model\Product\Url as ProductUrl;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ResourceModel\Product as ProductResource;
use Magento\CatalogInventory\Api\Data\StockItemInterface;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Phrase;
use Magento\InventorySalesApi\Api\GetProductSalableQtyInterface;
use Magento\Store\Model\StoreManagerInterface;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\MockObject\MockObject;

#[AllowMockObjectsWithoutExpectations]
class InventoryProductRecordBuilderTest extends TestCase
{
    private null|(GetProductSalableQtyInterface&MockObject) $salableQty = null;
    private null|(StockHelper&MockObject) $stockHelper = null;
    private null|(StockRegistryInterface&MockObject) $stockRegistry = null;
    private null|(ConfigHelper&MockObject) $configHelper = null;
    private null|(Product&MockObject) $product = null;

    private null|InventoryProductRecordBuilder $recordBuilder = null;

    protected function setUp(): void
    {
        $this->salableQty = $this->createMock(GetProductSalableQtyInterface::class);
        $this->stockHelper = $this->createMock(StockHelper::class);
        $this->stockRegistry = $this->createMock(StockRegistryInterface::class);
        $this->configHelper = $this->createMock(ConfigHelper::class);
        $this->product = $this->createMock(Product::class);

        $this->configHelper->method('isAttributeInList')->willReturn(true);
        $this->stockHelper->method('getStockId')->willReturn(2);
        $this->product->method('getSku')->willReturn('composite-sku');
        $this->product->method('getId')->willReturn(7);
        $this->product->method('getStoreId')->willReturn(1);

        $this->recordBuilder = new InventoryProductRecordBuilder(
            $this->salableQty,
            $this->stockHelper,
            $this->createMock(ManagerInterface::class),
            $this->createMock(DiagnosticsLogger::class),
            $this->createMock(Visibility::class),
            $this->createMock(StoreManagerInterface::class),
            $this->configHelper,
            $this->createMock(CategoryHelper::class),
            $this->createMock(CategoryRecordBuilder::class),
            $this->createMock(AlgoliaConnector::class),
            $this->createMock(ImageHelper::class),
            $this->stockRegistry,
            $this->createMock(PriceManager::class),
            $this->createMock(ProductUrl::class),
            $this->createMock(ProductResource::class),
            $this->createMock(GalleryReadHandler::class)
        );
    }

    public function testAddStockQtyUsesSalableQtyForSourceItemProducts(): void
    {
        $this->salableQty->expects($this->once())
            ->method('execute')
            ->with('composite-sku', 2)
            ->willReturn(12.0);

        $result = $this->invokeMethod($this->recordBuilder, 'addStockQty', [
            [],
            ['in_stock' => true],
            [],
            $this->product,
        ]);

        $this->assertSame(12, $result['stock_qty']);
    }

    public function testAddStockQtyFallsBackToLegacyQtyForProductsWithoutSourceItemSupport(): void
    {
        $this->salableQty->method('execute')->willThrowException(
            new InputException(new Phrase('Can\'t check requested quantity for products without Source Items support.'))
        );

        $stockItem = $this->createMock(StockItemInterface::class);
        $stockItem->method('getQty')->willReturn(0.0);
        $this->stockRegistry->method('getStockItem')->with(7)->willReturn($stockItem);

        $result = $this->invokeMethod($this->recordBuilder, 'addStockQty', [
            [],
            ['in_stock' => true],
            [],
            $this->product,
        ]);

        $this->assertSame(0, $result['stock_qty']);
        $this->assertTrue($result['in_stock']);
    }

    public function testAddStockQtySkipsSalableQtyForOutOfStockProducts(): void
    {
        $this->salableQty->expects($this->never())->method('execute');

        $result = $this->invokeMethod($this->recordBuilder, 'addStockQty', [
            [],
            ['in_stock' => false],
            [],
            $this->product,
        ]);

        $this->assertSame(0, $result['stock_qty']);
    }
}
