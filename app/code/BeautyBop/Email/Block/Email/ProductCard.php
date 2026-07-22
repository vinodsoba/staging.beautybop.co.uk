<?php

namespace BeautyBop\Email\Block\Email;

use Magento\Framework\View\Element\Template;
use Magento\Sales\Model\Order\Item as OrderItem;
use BeautyBop\Email\Helper\ProductHelper;

class ProductCard extends Template
{
    private ProductHelper $productHelper;

    public function __construct(
        Template\Context $context,
        ProductHelper $productHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->productHelper = $productHelper;
    }

    /**
     * Load Product
     */
    public function getProduct(OrderItem $item)
    {
        return $this->productHelper->getProduct(
            (int)$item->getProductId()
        );
    }

    /**
     * Product Image
     */
    public function getProductImage(OrderItem $item): string
    {
        return $this->productHelper
            ->getImageUrl(
                $this->getProduct($item)
            );
    }

    /**
     * Product URL
     */
    public function getProductUrl(OrderItem $item): string
    {
        return $this->productHelper
            ->getProductUrl(
                $this->getProduct($item)
            );
    }
}