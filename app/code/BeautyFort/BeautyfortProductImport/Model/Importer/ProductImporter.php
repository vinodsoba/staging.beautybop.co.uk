<?php
declare(strict_types=1);

namespace BeautyFort\BeautyfortProductImport\Model\Importer;

use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use BeautyFort\BeautyfortProductImport\Helper\Api;
use BeautyFort\BeautyfortProductImport\Helper\Price;
use BeautyFort\BeautyfortProductImport\Helper\Content;
use BeautyFort\BeautyfortProductImport\Helper\Image;
use BeautyFort\BeautyfortProductImport\Logger\Logger;
use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;

class ProductImporter
{
    /** @var Api */
    private $api;

    /** @var Price */
    private $price;

    /** @var Content */
    private $content;

    /** @var Image */
    private $image;

    /** @var ProductFactory */
    private $productFactory;

    /** @var ProductRepositoryInterface */
    private $productRepository;

    /** @var Filesystem */
    private $filesystem;

    /** @var Logger */
    private $logger;

    public function __construct(
        Api $api,
        Price $price,
        Content $content,
        Image $image,
        ProductFactory $productFactory,
        ProductRepositoryInterface $productRepository,
        Filesystem $filesystem,
        Logger $logger
    ) {
        $this->api = $api;
        $this->price = $price;
        $this->content = $content;
        $this->image = $image;
        $this->productFactory = $productFactory;
        $this->productRepository = $productRepository;
        $this->filesystem = $filesystem;
        $this->logger = $logger;
    }

    /**
     * Import ONE product safely
     */
    public function import(string $sku): bool
    {
        $items = $this->api->fetchProductBySku($sku);

        if (empty($items) || !isset($items[0])) {
            $this->logger->warning('🟡 No API product returned', ['sku' => $sku]);
            return false;
        }

        $apiProduct = $items[0];

        try {
            $product = $this->productRepository->get($apiProduct->StockCode);
            $this->logger->info('🔁 Updating existing product', ['sku' => $sku]);
        } catch (NoSuchEntityException $e) {
            $product = $this->productFactory->create();
            $product->setSku($apiProduct->StockCode);
        }

        // Core data
        $product->setName($apiProduct->Name);
        $product->setTypeId('simple');
        $product->setAttributeSetId(4);
        $product->setStatus(1);
        $product->setVisibility(4);

        $product->setData('beautyfort_source', 1);


        // Pricing
        $product->setPrice(
            $this->price->calculatePrice(
                (float)$apiProduct->UnitPrice->Amount
            )
        );

        // Stock
        $product->setStockData([
            'qty' => (int)$apiProduct->QuantityAvailable,
            'is_in_stock' => ((int)$apiProduct->QuantityAvailable > 0)
        ]);

        // SEO
        $product->setMetaTitle($this->content->buildMetaTitle($apiProduct->Name));
        $product->setMetaDescription($this->content->buildMetaDescription($apiProduct->Name));
        $product->setMetaKeyword($this->content->buildMetaKeywords($apiProduct->Name));
        $product->setShortDescription($this->content->buildShortDescription($apiProduct->Name));
        $product->setDescription($this->content->buildDescription($apiProduct));

        // Save product first (Magento requirement)
        $this->productRepository->save($product);

        // Image
        $imageUrl = $apiProduct->HighResImageUrl ?: $apiProduct->ThumbnailImageUrl;
        if ($imageUrl) {
            $mediaTmp = $this->filesystem
                ->getDirectoryWrite(DirectoryList::MEDIA)
                ->getAbsolutePath('tmp/catalog/product');

            if (!is_dir($mediaTmp)) {
                mkdir($mediaTmp, 0755, true);
            }

            $tmpFile = $mediaTmp . '/' . uniqid('bf_') . '.jpg';

            $this->image->downloadAndResize($imageUrl, $tmpFile);

            $product->addImageToMediaGallery(
                $tmpFile,
                ['image', 'small_image', 'thumbnail'],
                false,
                false
            );

            $this->productRepository->save($product);
            @unlink($tmpFile);
        }

        $this->logger->info('✅ Product imported', ['sku' => $sku]);

        return true;
    }
}
