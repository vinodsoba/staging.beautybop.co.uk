<?php

declare(strict_types=1);

namespace BeautyFort\BeautyfortProductImport\Model;

use Magento\InventoryApi\Api\SourceItemsSaveInterface;
use Magento\InventoryApi\Api\Data\SourceItemInterfaceFactory;
use Magento\InventoryApi\Api\Data\SourceItemInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;
use BeautyFort\BeautyfortProductImport\Helper\Api;
use BeautyFort\BeautyfortProductImport\Helper\Price;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use BeautyFort\BeautyfortProductImport\Logger\Logger;
use Magento\Framework\App\State;
use Magento\Framework\App\Area;

class PriceUpdater
{
    /** @var CollectionFactory */
    private $productCollectionFactory;

    /** @var ProductRepositoryInterface */
    private $productRepository;

    /** @var Api */
    private $api;

    /** @var Price */
    private $price;

    /** @var Logger */
    private $logger;

    /** @var State */
    private $appState;

    /** @var StockRegistryInterface */
    private $stockRegistry;

    /** @var SourceItemsSaveInterface */
    private $sourceItemsSave;

    /** @var SourceItemInterfaceFactory */
    private $sourceItemFactory;

    public function __construct(
        CollectionFactory $productCollectionFactory,
        ProductRepositoryInterface $productRepository,
        Api $api,
        Price $price,
        StockRegistryInterface $stockRegistry,
        Logger $logger,
        State $appState,
        SourceItemsSaveInterface $sourceItemsSave,
        SourceItemInterfaceFactory $sourceItemFactory
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->productRepository = $productRepository;
        $this->api = $api;
        $this->price = $price;
        $this->stockRegistry = $stockRegistry;
        $this->logger = $logger;
        $this->appState = $appState;
        $this->sourceItemsSave = $sourceItemsSave;
        $this->sourceItemFactory = $sourceItemFactory;
    }

    public function execute(): void
    {
        try {
            $this->appState->setAreaCode(Area::AREA_ADMINHTML);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            // Ignore if already set
        }

        $this->logger->info('🕒 PRICE CRON START');

        $supplierProducts = $this->api->getStockFile();

        $this->logger->info('Supplier stock file downloaded', [
            'count' => count($supplierProducts)
        ]);

        if (empty($supplierProducts)) {
            $this->logger->error('No supplier products returned. Aborting price update.');
            return;
        }

        $supplierLookup = [];

        foreach ($supplierProducts as $item) {
            if (empty($item['StockCode'])) {
                continue;
            }

            $stockCode = strtoupper(trim((string)$item['StockCode']));
            $supplierLookup[$stockCode] = $item;
        }

        $this->logger->info('Supplier lookup built', [
            'count' => count($supplierLookup)
        ]);

        $this->logger->info('First supplier lookup keys', [
            'keys' => array_slice(array_keys($supplierLookup), 0, 20)
        ]);

        $updatedCount = 0;
        $checkedCount = 0;
        $unchangedCount = 0;
        $errorCount = 0;
        $stockUpdatedCount = 0;

        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToSelect(['sku', 'price', 'beautyfort_source']);
        $collection->addAttributeToFilter('beautyfort_source', 1);

        $this->logger->info('Magento BeautyFort products loaded', [
            'count' => $collection->getSize()
        ]);

        foreach ($collection as $product) {
            try {
                $sku = trim((string)$product->getSku());
                $lookupSku = strtoupper($sku);

                /*
                 * TEMPORARY SINGLE-SKU TEST
                 * Uncomment while testing A025786 only.
                 */
                
                /*if ($sku !== 'A025786') {
                    continue;
                }*/
                

                if (!isset($supplierLookup[$lookupSku])) {
                    $checkedCount++;

                    $this->logger->warning(
                        'Supplier SKU not found - setting product out of stock',
                        ['sku' => $sku]
                    );

                    try {
                        $stockItem = $this->stockRegistry->getStockItemBySku($sku);

                        $currentQty = (float)$stockItem->getQty();
                        $currentIsInStock = (bool)$stockItem->getIsInStock();
                        $currentUseConfigManageStock = (bool)$stockItem->getUseConfigManageStock();

                        $stockNeedsUpdate = ($currentQty != 0 || $currentIsInStock);
                        $stockConfigNeedsRepair = !$currentUseConfigManageStock;

                        if ($stockNeedsUpdate || $stockConfigNeedsRepair) {
                            $didStockChange = false;

                            if ($stockConfigNeedsRepair) {
                                $didStockChange = $this->ensureManageStockUsesConfig($sku) || $didStockChange;
                            }

                            if ($stockNeedsUpdate) {
                                $this->updateMsiStock($sku, 0);
                                $didStockChange = true;
                            }

                            if ($didStockChange) {
                                $stockUpdatedCount++;
                            }

                            $updatedCount++;

                            $this->logger->info(
                                'Missing supplier SKU set out of stock',
                                [
                                    'sku' => $sku,
                                    'old_qty' => $currentQty,
                                    'new_qty' => 0,
                                    'old_is_in_stock' => $currentIsInStock,
                                    'new_is_in_stock' => false,
                                    'old_use_config_manage_stock' => $currentUseConfigManageStock,
                                    'new_use_config_manage_stock' => true
                                ]
                            );
                        } else {
                            $unchangedCount++;

                            $this->logger->info(
                                'Missing supplier SKU already out of stock',
                                ['sku' => $sku]
                            );
                        }
                    } catch (\Throwable $e) {
                        $errorCount++;

                        $this->logger->error(
                            'Failed setting missing supplier SKU out of stock',
                            [
                                'sku' => $sku,
                                'message' => $e->getMessage()
                            ]
                        );
                    }

                    continue;
                }

                $supplierData = $supplierLookup[$lookupSku];

                $stockItem = $this->stockRegistry->getStockItemBySku($sku);

                $currentQty = (int)$stockItem->getQty();
                $newQty = (int)($supplierData['StockLevel'] ?? 0);

                $currentIsInStock = (bool)$stockItem->getIsInStock();
                $newIsInStock = $newQty > 0;

                $currentUseConfigManageStock = (bool)$stockItem->getUseConfigManageStock();
                $stockConfigNeedsRepair = !$currentUseConfigManageStock;

                $this->logger->info('Supplier lookup hit', [
                    'sku' => $sku,
                    'price' => $supplierData['Price'] ?? null,
                    'rrp' => $supplierData['RRP'] ?? null,
                    'stock' => $supplierData['StockLevel'] ?? null,
                ]);

                $checkedCount++;

                $oldPrice = (float)$product->getPrice();
                $supplierCost = (float)($supplierData['Price'] ?? 0);
                $newPrice = $this->price->calculatePrice($supplierCost);

                $currentRrp = (float)$product->getData('beautyfort_rrp');
                $newRrp = (float)($supplierData['RRP'] ?? 0);

                $this->logger->info('RRP comparison', [
                    'sku' => $sku,
                    'current_rrp' => $currentRrp,
                    'new_rrp' => $newRrp
                ]);

                $hasChanges = false;

                if ($currentRrp != $newRrp) {
                    $product->setData('beautyfort_rrp', $newRrp);
                    $hasChanges = true;

                    $this->logger->info('RRP changed', [
                        'sku' => $sku,
                        'old' => $currentRrp,
                        'new' => $newRrp
                    ]);
                }

                $this->logger->info('Price comparison', [
                    'sku' => $sku,
                    'old_price' => $oldPrice,
                    'new_price' => $newPrice
                ]);

                if ($newPrice != $oldPrice) {
                    $product->setPrice($newPrice);
                    $hasChanges = true;

                    $this->logger->info('Price changed', [
                        'sku' => $sku,
                        'old_price' => $oldPrice,
                        'new_price' => $newPrice
                    ]);
                }

                $stockNeedsUpdate = (
                    $currentQty != $newQty ||
                    $currentIsInStock != $newIsInStock
                );

                if ($stockNeedsUpdate || $stockConfigNeedsRepair) {
                    $hasChanges = true;
                }

                if ($hasChanges) {
                    try {
                        $this->logger->info('Saving product', ['sku' => $sku]);

                        $product = $this->productRepository->get($sku);
                        $product->setPrice($newPrice);
                        $product->setData('beautyfort_rrp', $newRrp);
                        $this->productRepository->save($product);

                        $didStockChange = false;

                        if ($stockConfigNeedsRepair) {
                            $didStockChange = $this->ensureManageStockUsesConfig($sku) || $didStockChange;
                        }

                        if ($stockNeedsUpdate) {
                            $this->updateMsiStock($sku, (float)$newQty);
                            $didStockChange = true;

                            $this->logger->info('Stock changed', [
                                'sku' => $sku,
                                'old_qty' => $currentQty,
                                'new_qty' => $newQty
                            ]);
                        }

                        if ($didStockChange) {
                            $stockUpdatedCount++;
                        }

                        $updatedCount++;
                    } catch (\Throwable $e) {
                        $errorCount++;

                        $this->logger->error('Save failed', [
                            'sku' => $sku,
                            'message' => $e->getMessage(),
                            'trace' => $e->getTraceAsString()
                        ]);

                        continue;
                    }
                } else {
                    $unchangedCount++;

                    $this->logger->info('Product unchanged', [
                        'sku' => $sku
                    ]);
                }
            } catch (\Throwable $e) {
                $errorCount++;

                $this->logger->error('❌ Price update failed', [
                    'sku' => $product->getSku(),
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }

        $this->logger->info('✅ PRICE CRON SUMMARY', [
            'checked' => $checkedCount,
            'updated' => $updatedCount,
            'stock_updated' => $stockUpdatedCount,
            'unchanged' => $unchangedCount,
            'errors' => $errorCount
        ]);
    }

    private function updateMsiStock(string $sku, float $qty): void
    {
        $sourceItem = $this->sourceItemFactory->create();

        $sourceItem->setSourceCode('default');
        $sourceItem->setSku($sku);
        $sourceItem->setQuantity($qty);

        $sourceItem->setStatus(
            $qty > 0
                ? SourceItemInterface::STATUS_IN_STOCK
                : SourceItemInterface::STATUS_OUT_OF_STOCK
        );

        $this->sourceItemsSave->execute([$sourceItem]);
    }

    private function ensureManageStockUsesConfig(string $sku): bool
    {
        $stockItem = $this->stockRegistry->getStockItemBySku($sku);

        if ((bool)$stockItem->getUseConfigManageStock()) {
            return false;
        }

        $stockItem->setUseConfigManageStock(true);

        $this->stockRegistry->updateStockItemBySku(
            $sku,
            $stockItem
        );

        $this->logger->info('Manage Stock configuration repaired', [
            'sku' => $sku,
            'use_config_manage_stock' => 1
        ]);

        return true;
    }
}
