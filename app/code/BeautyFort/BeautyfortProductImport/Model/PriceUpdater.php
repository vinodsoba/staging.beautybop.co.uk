<?php
declare(strict_types=1);

namespace BeautyFort\BeautyfortProductImport\Model;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;
use BeautyFort\BeautyfortProductImport\Helper\Api;
use BeautyFort\BeautyfortProductImport\Helper\Price;
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

    public function __construct(
        CollectionFactory $productCollectionFactory,
        ProductRepositoryInterface $productRepository,
        Api $api,
        Price $price,
        Logger $logger,
        State $appState
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->productRepository = $productRepository;
        $this->api = $api;
        $this->price = $price;
        $this->logger = $logger;
        $this->appState = $appState;
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

        $supplierLookup = [];

        foreach ($supplierProducts as $item) {

            if (empty($item['StockCode'])) {
                continue;
            }

            $supplierLookup[$item['StockCode']] = $item;
        }

        $this->logger->info('Supplier lookup built', [
            'count' => count($supplierLookup)
        ]);

       

        $updatedCount = 0;

        /**
         * OLD BULK FETCH
         * 1️⃣ Temporarily disabled while migrating to SKU lookups
         */

        $checkedCount = 0;
        $unchangedCount = 0;
        $errorCount = 0;
   

        /**
         * 2️⃣ Load Magento Beautyfort products
         */
        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToSelect(['sku', 'price', 'beautyfort_source']);
        $collection->addAttributeToFilter('beautyfort_source', 1);

        $this->logger->info('Magento BeautyFort products loaded', [
            'count' => $collection->getSize()
        ]);

        /**
         * 3️⃣ Loop Magento products and match by SKU in memory
         */
        foreach ($collection as $product) {

            try {
                
                $sku = $product->getSku();

                if($sku !== 'T240'){
                    continue;
                }

                // Skip if supplier does not have this SKU
                $this->logger->info('Checking SKU', [
                'sku' => $sku
                ]);

            if (!isset($supplierLookup[$sku])) {

                $this->logger->warning(
                    'Supplier SKU not found',
                    ['sku' => $sku]
                );

                continue;
            }

            $supplierData = $supplierLookup[$sku];

            $this->logger->info('Supplier lookup hit', [
                'sku'   => $sku,
                'price' => $supplierData['Price'] ?? null,
                'rrp'   => $supplierData['RRP'] ?? null,
                'stock' => $supplierData['StockLevel'] ?? null,
            ]);

            $checkedCount++;

            
            $oldPrice = (float)$product->getPrice();
            $supplierCost = (float)($supplierData['Price'] ?? 0);

            $newPrice = $this->price->calculatePrice($supplierCost);

            $currentRrp = (float) $product->getData('beautyfort_rrp');
            $newRrp = (float) ($supplierData['RRP'] ?? 0);

            $this->logger->info('RRP comparison', [
                'sku'         => $sku,
                'current_rrp' => $currentRrp,
                'new_rrp'     => $newRrp
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


            if ($hasChanges) {

                $this->logger->info('Saving product', [
                    'sku' => $sku
                ]);

                $this->productRepository->save($product);

                $updatedCount++;

            } else {

                $unchangedCount++;

                $this->logger->info('Product unchanged', [
                    'sku' => $sku
                ]);

            }

            } catch (\Throwable $e) {

                $errorCount++;

                $this->logger->error('❌ Price update failed', [
                    'sku'   => $product->getSku(),
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                            }
        }

        $this->logger->info('✅ PRICE CRON SUMMARY', [

            'checked'   => $checkedCount,
            'updated'   => $updatedCount,
            'unchanged' => $unchangedCount,
            'errors'    => $errorCount

        ]);
    }

}
