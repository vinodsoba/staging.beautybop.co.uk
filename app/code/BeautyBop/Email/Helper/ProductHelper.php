<?php

namespace BeautyBop\Email\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Helper\Image;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Api\Data\ProductInterface;

class ProductHelper extends AbstractHelper
{
    private ProductRepositoryInterface $productRepository;
    private Image $imageHelper;
    private StoreManagerInterface $storeManager;

    public function __construct(
        Context $context,
        ProductRepositoryInterface $productRepository,
        Image $imageHelper,
        StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);

        $this->productRepository = $productRepository;
        $this->imageHelper = $imageHelper;
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
    public function getImageUrl(ProductInterface $product): string
    {
        return $this->imageHelper
            ->init($product, 'product_base_image')
            ->getUrl();
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