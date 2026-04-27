<?php
public function execute()
{
    if (!$this->helper->isProductImportEnabled()) {
        $this->logger->info('Beautyfort product import disabled');
        return;
    }

    $products = $this->api->fetchProducts();

    foreach ($products as $product) {
        if (!$this->productExists($product->StockCode)) {
            $this->logger->info(
                'MISSING SKU: ' . $product->StockCode
            );
        }
    }
}


public function productExists($sku)
{
    try {
        $this->productRepository->get($sku);
        return true;
    } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
        return false;
    }
}

