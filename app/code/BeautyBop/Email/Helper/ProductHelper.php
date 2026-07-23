<?php

namespace BeautyBop\Email\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\UrlInterface;


class ProductHelper extends AbstractHelper
{
    private ProductRepositoryInterface $productRepository;
    private StoreManagerInterface $storeManager;

    public function __construct(
        Context $context,
        ProductRepositoryInterface $productRepository,
        StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);

        $this->productRepository = $productRepository;
        $this->storeManager = $storeManager;
    }

    /**
     * Load Product
     */
    public function getProduct(int $productId): ProductInterface
    {
        return $this->productRepository->getById($productId);
    }

    /**
     * Product Image
     */
    public function getImageUrl(int $productId): string
    {
        $product = $this->getProduct($productId);

        $image = $product->getImage();

        if (!$image || $image === 'no_selection') {
            return '';
        }

        return $this->storeManager
            ->getStore()
            ->getBaseUrl(UrlInterface::URL_TYPE_MEDIA)
            . 'catalog/product'
            . $image;
    }

    /**
     * Product URL
     */
    public function getProductUrl(ProductInterface $product): string
    {
        return $product->getProductUrl();
    }

    /**
     * Product Name
     */
    public function getName(ProductInterface $product): string
    {
        return $product->getName();
    }
}