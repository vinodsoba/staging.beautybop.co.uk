<?php
namespace BeautyFort\BeautyfortProductImport\Model;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use BeautyFort\BeautyfortProductImport\Helper\Api as ApiHelper;
use BeautyFort\BeautyfortProductImport\Helper\Image;
use BeautyFort\BeautyfortProductImport\Helper\Price;
use BeautyFort\BeautyfortProductImport\Helper\Content;
use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;

class BulkImporter
{
    protected ProductRepositoryInterface $productRepository;
    protected ProductFactory $productFactory;
    protected ApiHelper $apiHelper;
    protected Price $price;
    protected Image $image;
    protected Filesystem $filesystem;
    protected Content $content;

    private const TEST_IMPORT_LIMIT = 5;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        ProductFactory $productFactory,
        ApiHelper $apiHelper,
        Price $price,
        Image $image,
        Filesystem $filesystem,
        Content $content
    ) {
        $this->productRepository = $productRepository;
        $this->productFactory = $productFactory;
        $this->apiHelper = $apiHelper;
        $this->price = $price;
        $this->content = $content;
        $this->image = $image;
        $this->filesystem = $filesystem;
    }

    public function import(array $skus, array $categoryIds): array
    {
        $limit = self::TEST_IMPORT_LIMIT;
        $count = 0;

        $results = [];
        $importedCount = 0;
        $skippedExisting = 0;
        $skippedNonFragrance = 0;

        foreach ($skus as $sku) {

            if ($count >= $limit) {
                break;
            }

            $sku = trim($sku);

            if ($sku === '') {
                continue;
            }

            try {

                /** Skip if product already exists */
                try {
                    $this->productRepository->get($sku);
                    $results[$sku] = 'Skipped - already exists';
                    $skippedExisting++;
                    continue;

                } catch (NoSuchEntityException $e) {
                    // Product does not exist → continue
                }

                /** Fetch product from API with rate-limit protection */
                try {

                    $apiResponse = $this->apiHelper->fetchProductBySku($sku);

                } catch (\Exception $e) {

                    if (strpos($e->getMessage(), 'Rate Limit') !== false) {

                        sleep(10);

                        $apiResponse = $this->apiHelper->fetchProductBySku($sku);

                    } else {
                        throw $e;
                    }
                }

                sleep(1); // throttle API calls

                if (empty($apiResponse) || !isset($apiResponse[0])) {
                    $results[$sku] = 'Failed - not found in API';
                    continue;
                }

                $item = $apiResponse[0];
                $name = $item->Name ?? '';

                /** Skip non-fragrance products */
                if (!$this->isFragrance($name)) {
                    $results[$sku] = 'Skipped - non fragrance';
                    $skippedNonFragrance++;
                    continue;
                }

                /** Create product */
                $product = $this->productFactory->create();

                $count++;

                $product->setSku($sku);
                $product->setAttributeSetId(9);
                $product->setTypeId('simple');
                $product->setWebsiteIds([1]);
                $product->setStoreId(0);
                $product->setName($name);

                /** Price */
                $price = isset($item->UnitPrice->Amount)
                    ? (float)$item->UnitPrice->Amount
                    : 0;

                $product->setPrice(
                    $this->price->calculatePrice($price)
                );

                /** Stock */
                $qty = (int)($item->QuantityAvailable ?? 0);

                $product->setStockData([
                    'qty' => $qty,
                    'is_in_stock' => ($qty > 0)
                ]);

                /** SEO content */
                $product->setMetaTitle($this->content->buildMetaTitle($name));
                $product->setMetaDescription($this->content->buildMetaDescription($name));
                $product->setMetaKeyword($this->content->buildMetaKeywords($name));
                $product->setShortDescription($this->content->buildShortDescription($name));
                $product->setDescription($this->content->buildDescription($item));

                /** Status */
                $product->setStatus(1);
                $product->setVisibility(4);
                $product->setCategoryIds($categoryIds);

                /** Mark supplier source */
                $product->setData('beautyfort_source', 1);

                /** Save product first */
                $this->productRepository->save($product);

                /** Image */
                $imageUrl = null;

                if (!empty($item->HighResImageUrl)) {
                    $imageUrl = $item->HighResImageUrl;
                } elseif (!empty($item->MediumImageUrl)) {
                    $imageUrl = $item->MediumImageUrl;
                } elseif (!empty($item->ThumbnailImageUrl)) {
                    $imageUrl = $item->ThumbnailImageUrl;
                }

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
                        ['image','small_image','thumbnail'],
                        false,
                        false
                    );

                    $this->productRepository->save($product);

                    @unlink($tmpFile);
                }

                $results[$sku] = 'Imported';
                $importedCount++;

            } catch (\Exception $e) {

                $results[$sku] = $e->getMessage();
            }
        }

        return [
            'imported' => $importedCount,
            'skipped_non_fragrance' => $skippedNonFragrance,
            'skipped_existing' => $skippedExisting,
            'items' => $results
        ];
    }

    public function importByBrand(string $brand, array $categoryIds): array
    {
        $apiProducts = $this->apiHelper->fetchProductsByBrand($brand);

        /** Limit brand imports during testing */
        $apiProducts = array_slice($apiProducts, 0, self::TEST_IMPORT_LIMIT);

        $skus = [];

        foreach ($apiProducts as $item) {

            if (!empty($item->StockCode)) {
                $skus[] = $item->StockCode;
            }

        }

        if (empty($skus)) {
            return [
                'imported' => 0,
                'skipped_non_fragrance' => 0,
                'skipped_existing' => 0,
                'items' => []
            ];
        }

        return $this->import($skus, $categoryIds);
    }

    private function isFragrance(string $name): bool
    {
        $name = strtolower($name);

        $exclude = [
            'shampoo',
            'conditioner',
            'deodorant',
            'gift set',
            'body lotion',
            'body moisturiser',
            'body cream',
            'shower gel',
            'aftershave balm',
            'hair mist'
        ];

        foreach ($exclude as $term) {
            if (strpos($name, $term) !== false) {
                return false;
            }
        }

        return true;
    }

    private function detectGender(string $name): string
    {
        $name = strtolower($name);

        if (preg_match('/(men|man|pour homme)/', $name)) {
            return 'men';
        }

        if (preg_match('/(women|woman|pour femme|lady)/', $name)) {
            return 'women';
        }

        return 'unisex';
    }
}