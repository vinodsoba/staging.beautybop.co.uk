<?php
declare(strict_types=1);

namespace BeautyFort\BeautyfortProductImport\Model\Importer;

use BeautyFort\BeautyfortProductImport\Logger\Logger;

class BulkImporter
{
    /** @var ProductImporter */
    private  $importer;

    /** @var Logger */
    private  $logger;

  

    public function __construct(
        ProductImporter $importer,
        Logger $logger
    ) {
        $this->importer = $importer;
        $this->logger = $logger;
    }

    /**
     * Import multiple SKUs safely
     */
    public function import(array $skus): array
    {
        $results = [
            'success' => 0,
            'failed'  => 0
        ];

        foreach ($skus as $sku) {
            try {
                $success = $this->importer->import(trim($sku));
                $success ? $results['success']++ : $results['failed']++;
            } catch (\Throwable $e) {
                $results['failed']++;
                $this->logger->error('❌ Bulk import error', [
                    'sku' => $sku,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return $results;
    }
}
