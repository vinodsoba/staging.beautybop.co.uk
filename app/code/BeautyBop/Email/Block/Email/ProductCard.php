<?php

namespace BeautyBop\Email\Block\Email;

use Magento\Sales\Block\Order\Email\Items\Order\DefaultOrder;

use Magento\Framework\View\Element\Template\Context;
use Magento\Sales\Model\Order\Item as OrderItem;
use BeautyBop\Email\Helper\ProductHelper;

class ProductCard extends DefaultOrder
{
    private ProductHelper $productHelper;

    public function __construct(
        Context $context,
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
            ->getImageUrl($item->getProductId());
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

    public function getProductImageAttribute(OrderItem $item): string
    {
        return (string)$this->getProduct($item)->getImage();
    }

    public function getProductSku(OrderItem $item): string
    {
        return (string)$this->getProduct($item)->getSku();
    }
}