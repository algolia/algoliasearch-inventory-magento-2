<?php

declare(strict_types=1);

namespace Algolia\AlgoliaSearchInventory\Test\Unit\Helper;

use Algolia\AlgoliaSearch\Api\Product\ReplicaManagerInterface;
use Algolia\AlgoliaSearch\Helper\ConfigHelper;
use Algolia\AlgoliaSearch\Logger\DiagnosticsLogger;
use Algolia\AlgoliaSearch\Service\AlgoliaConnector;
use Algolia\AlgoliaSearch\Service\Index\IndexNameFetcher;
use Algolia\AlgoliaSearch\Service\Index\IndexOptionsBuilder;
use Algolia\AlgoliaSearch\Service\Index\Settings\IndexSettingsHandler;
use Algolia\AlgoliaSearch\Service\Product\FacetBuilder;
use Algolia\AlgoliaSearch\Service\Product\RecordBuilder as ProductRecordBuilder;
use Algolia\AlgoliaSearch\Test\TestCase;
use Algolia\AlgoliaSearchInventory\Helper\InventoryProductHelper;
use Algolia\AlgoliaSearchInventory\Helper\StockHelper;
use Magento\Catalog\Api\Data\ProductInterfaceFactory;
use Magento\Catalog\Model\Product\Type;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\CatalogInventory\Helper\Stock;
use Magento\Eav\Model\Config;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Phrase;
use Magento\InventoryCatalog\Model\ResourceModel\AddStockDataToCollection;
use Magento\Store\Model\StoreManagerInterface;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;

#[AllowMockObjectsWithoutExpectations]
class InventoryProductHelperTest extends TestCase
{
    private null|(AddStockDataToCollection&MockObject) $addStockDataToCollection = null;
    private null|(StockHelper&MockObject) $localStockHelper = null;
    private null|(Config&MockObject) $eavConfig = null;
    private null|(ConfigHelper&MockObject) $configHelper = null;
    private null|(AlgoliaConnector&MockObject) $algoliaConnector = null;
    private null|(IndexOptionsBuilder&MockObject) $indexOptionsBuilder = null;
    private null|(DiagnosticsLogger&MockObject) $logger = null;
    private null|(StoreManagerInterface&MockObject) $storeManager = null;
    private null|(ManagerInterface&MockObject) $eventManager = null;
    private null|(Visibility&MockObject) $visibility = null;
    private null|(Stock&MockObject) $deprecatedStockHelper = null;
    private null|(Type&MockObject) $productType = null;
    private null|(CollectionFactory&MockObject) $productCollectionFactory = null;
    private null|(IndexNameFetcher&MockObject) $indexNameFetcher = null;
    private null|(ReplicaManagerInterface&MockObject) $replicaManager = null;
    private null|(ProductInterfaceFactory&MockObject) $productFactory = null;
    private null|(ProductRecordBuilder&MockObject) $productRecordBuilder = null;
    private null|(FacetBuilder&MockObject) $facetBuilder = null;
    private null|(IndexSettingsHandler&MockObject) $indexSettingsHandler = null;

    private null|InventoryProductHelper $inventoryProductHelper = null;

    protected function setUp(): void
    {
        $this->addStockDataToCollection = $this->createMock(AddStockDataToCollection::class);
        $this->localStockHelper = $this->createMock(StockHelper::class);
        $this->eavConfig = $this->createMock(Config::class);
        $this->configHelper = $this->createMock(ConfigHelper::class);
        $this->algoliaConnector = $this->createMock(AlgoliaConnector::class);
        $this->indexOptionsBuilder = $this->createMock(IndexOptionsBuilder::class);
        $this->logger = $this->createMock(DiagnosticsLogger::class);
        $this->storeManager = $this->createMock(StoreManagerInterface::class);
        $this->eventManager = $this->createMock(ManagerInterface::class);
        $this->visibility = $this->createMock(Visibility::class);
        $this->deprecatedStockHelper = $this->createMock(Stock::class);
        $this->productType = $this->createMock(Type::class);
        $this->productCollectionFactory = $this->createMock(CollectionFactory::class);
        $this->indexNameFetcher = $this->createMock(IndexNameFetcher::class);
        $this->replicaManager = $this->createMock(ReplicaManagerInterface::class);
        $this->productFactory = $this->createMock(ProductInterfaceFactory::class);
        $this->productRecordBuilder = $this->createMock(ProductRecordBuilder::class);
        $this->facetBuilder = $this->createMock(FacetBuilder::class);
        $this->indexSettingsHandler = $this->createMock(IndexSettingsHandler::class);

        $this->inventoryProductHelper = new InventoryProductHelper(
            $this->addStockDataToCollection,
            $this->localStockHelper,
            $this->eavConfig,
            $this->configHelper,
            $this->algoliaConnector,
            $this->indexOptionsBuilder,
            $this->logger,
            $this->storeManager,
            $this->eventManager,
            $this->visibility,
            $this->deprecatedStockHelper,
            $this->productType,
            $this->productCollectionFactory,
            $this->indexNameFetcher,
            $this->replicaManager,
            $this->productFactory,
            $this->productRecordBuilder,
            $this->facetBuilder,
            $this->indexSettingsHandler
        );
    }

    public function testSetsFlagAfterSuccessfulStockJoin(): void
    {
        $this->configHelper->method('getShowOutOfStock')->willReturn(false);
        $this->localStockHelper->method('getStockId')->willReturn(1);
        $this->addStockDataToCollection->expects($this->once())->method('execute');

        $products = $this->createMock(Collection::class);
        $products->expects($this->once())->method('setFlag')->with('has_stock_status_filter', true);

        $this->invokeMethod($this->inventoryProductHelper, 'addStockFilter', [$products, 1]);
    }

    #[DataProvider('showOutOfStockProvider')]
    public function testPassesInverseOfShowOutOfStockAsFilterFlag(bool $showOutOfStock, bool $expectedIsFilterInStock): void
    {
        $this->configHelper->method('getShowOutOfStock')->willReturn($showOutOfStock);
        $this->localStockHelper->method('getStockId')->willReturn(1);
        $this->addStockDataToCollection->expects($this->once())
            ->method('execute')
            ->with($this->anything(), $expectedIsFilterInStock, $this->anything());

        $products = $this->createMock(Collection::class);

        $this->invokeMethod($this->inventoryProductHelper, 'addStockFilter', [$products, 1]);
    }

    public static function showOutOfStockProvider(): array
    {
        return [
            'show out of stock enabled, so core should not filter in-stock only' => [true, false],
            'show out of stock disabled, so core should filter in-stock only' => [false, true],
        ];
    }

    public function testForwardsResolvedStockId(): void
    {
        $this->configHelper->method('getShowOutOfStock')->willReturn(false);
        $this->localStockHelper->expects($this->once())->method('getStockId')->with(1)->willReturn(7);
        $this->addStockDataToCollection->expects($this->once())
            ->method('execute')
            ->with($this->anything(), $this->anything(), 7);

        $products = $this->createMock(Collection::class);

        $this->invokeMethod($this->inventoryProductHelper, 'addStockFilter', [$products, 1]);
    }

    public function testDoesNotSetFlagWhenStockJoinFails(): void
    {
        $this->configHelper->method('getShowOutOfStock')->willReturn(false);
        $this->localStockHelper->method('getStockId')->willReturn(1);
        $this->addStockDataToCollection->method('execute')
            ->willThrowException(new LocalizedException(new Phrase('Error applying MSI stock filter.')));

        $products = $this->createMock(Collection::class);
        $products->expects($this->never())->method('setFlag');

        $this->logger->expects($this->once())->method('error');

        $this->invokeMethod($this->inventoryProductHelper, 'addStockFilter', [$products, 1]);
    }

    public function testDoesNotSetFlagWhenStockIdResolutionFails(): void
    {
        $this->localStockHelper->method('getStockId')
            ->willThrowException(NoSuchEntityException::singleField('storeId', 1));

        $this->addStockDataToCollection->expects($this->never())->method('execute');

        $products = $this->createMock(Collection::class);
        $products->expects($this->never())->method('setFlag');

        $this->logger->expects($this->once())->method('error');

        $this->invokeMethod($this->inventoryProductHelper, 'addStockFilter', [$products, 1]);
    }
}
