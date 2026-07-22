<?php

namespace BeautyBop\Email\Block\Email;

use Magento\Sales\Block\Order\Email\Items\Order\DefaultOrder;

use Magento\Framework\View\Element\Template\Context;
use Magento\Sales\Model\Order\Item as OrderItem;
use BeautyBop\Email\Helper\ProductHelper;
use Magento\Catalog\Api\Data\ProductInterface;

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
    
    public function getProduct(OrderItem $item): ProductInterface
    {
        return $this->productHelper->getProduct(
            (int) $item->getProductId()
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

    /**
     * Set current order item.
     */
    public function setItem(OrderItem $item): self
    {
        $this->setData('item', $item);

        return $this;
    }

    /**
     * Get current order item.
     */
    public function getItem(): OrderItem
    {
        return $this->getData('item');
    }


    /**
     * Render the reusable product card template.
     */

    public function renderProductCard(OrderItem $item): string
    {
        $this->setItem($item);

        return $this->fetchView(
            $this->getTemplateFile('BeautyBop_Email::email/product-card.phtml')
        );
    }
}