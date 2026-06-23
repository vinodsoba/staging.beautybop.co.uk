<?php
declare(strict_types=1);

namespace BeautyFort\BeautyfortProductImport\Model;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;
use BeautyFort\BeautyfortProductImport\Helper\Api;
use BeautyFort\BeautyfortProductImport\Helper\Price;
use BeautyFort\BeautyfortProductImport\Logger\Logger;

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

    public function __construct(
        CollectionFactory $productCollectionFactory,
        ProductRepositoryInterface $productRepository,
        Api $api,
        Price $price,
        Logger $logger
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->productRepository = $productRepository;
        $this->api = $api;
        $this->price = $price;
        $this->logger = $logger;
    }

    public function execute(): void
    {
        $this->logger->info('🕒 PRICE CRON START');

        /*$this->logger->info('🔎 CRON credential debug', [
            'username' => $this->config->getUsername(),
            'password_length' => strlen($this->config->getPassword() ?? '')
        ]);
        */

        $updatedCount = 0;

        /**
         * 1️⃣ Fetch ALL supplier products once
         */

   

        $this->logger->info('DEBUG: About to call fetchAllProducts');

        $supplierProducts = $this->api->fetchAllProducts();

        $this->logger->info('DEBUG: fetchAllProducts returned', [
            'count' => count($supplierProducts)
        ]);

        if (empty($supplierProducts)) {
            $this->logger->warning('⚠️ No supplier products returned from bulk API');
            return;
        }

        /**
         * 2️⃣ Load Magento Beautyfort products
         */
        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToSelect(['sku', 'price', 'beautyfort_source']);
        $collection->addAttributeToFilter('beautyfort_source', 1);

        $this->logger->info('Magento collection size', [
            'count' => $collection->getSize()
        ]);

        /**
         * 3️⃣ Loop Magento products and match by SKU in memory
         */
        foreach ($collection as $product) {

            try {
                $sku = $product->getSku();

                // Skip if supplier does not have this SKU
                if (!isset($supplierProducts[$sku])) {
                    continue;
                }

                $oldPrice = (float)$product->getPrice();
                $supplierCost = (float)$supplierProducts[$sku]->UnitPrice->Amount;

                $newPrice = $this->price->calculatePrice($supplierCost);

                $this->logger->info('Checking SKU', [
                    'sku' => $sku,
                    'old_price' => $oldPrice,
                    'new_price' => $newPrice
                ]);

                if ($newPrice != $oldPrice) {

                    $product->setPrice($newPrice);
                    $this->productRepository->save($product);

                    $updatedCount++;

                    $this->logger->info('💰 Price updated', [
                        'sku' => $sku,
                        'old_price' => $oldPrice,
                        'new_price' => $newPrice
                    ]);
                }

            } catch (\Throwable $e) {

                $this->logger->error('❌ Price update failed', [
                    'sku' => $product->getSku(),
                    'error' => $e->getMessage()
                ]);
            }
        }

        $this->logger->info('✅ PRICE CRON FINISHED', [
            'updated' => $updatedCount
        ]);
    }

}
