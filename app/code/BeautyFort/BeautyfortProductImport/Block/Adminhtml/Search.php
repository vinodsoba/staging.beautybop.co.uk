<?php

namespace BeautyFort\BeautyfortProductImport\Block\Adminhtml;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use BeautyFort\BeautyfortProductImport\Helper\Api;
use BeautyFort\BeautyfortProductImport\Logger\Logger;

class Search extends Template
{
    /**
     * @var Api
     */
    protected $api;

    /**
     * @var Logger
     */
    protected $logger;

    protected $_template = 'BeautyFort_BeautyfortProductImport::search.phtml';

    public function __construct(
        Context $context,
        Api $api,
        Logger $logger,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->api = $api;
        $this->logger = $logger;
    }

    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * Get SKU from request
     */
    public function getSku(): string
    {
        return (string) $this->getRequest()->getParam('sku');
    }

    /**
     * Fetch products from Beautyfort
     */
    public function getProducts(): array
    {
    $this->logger->info('🧱 Search::getProducts() HIT');

    $sku = trim((string)$this->getSku());

    $this->logger->info('🧱 SKU FROM REQUEST', ['sku' => $sku]);

    if ($sku === '') {
        $this->logger->warning('🧱 Empty SKU – returning early');
        return [];
    }

    $products = $this->api->fetchProductBySku($sku);

    $this->logger->info('🧱 Products returned to block', [
        'count' => count($products)
    ]);

    return $products;
    }


    /**
     * Form action
     */
    public function getFormAction(): string
    {
        return $this->getUrl('*/*/*');
    }
}
