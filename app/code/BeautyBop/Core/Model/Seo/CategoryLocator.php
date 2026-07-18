<?php

declare(strict_types=1);

namespace BeautyBop\Core\Model\Seo;

use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Psr\Log\LoggerInterface;
use Magento\Store\Model\StoreManagerInterface;

class CategoryLocator
{
    private CollectionFactory $categoryCollectionFactory;
    private LoggerInterface $logger;
    private StoreManagerInterface $storeManager;

    public function __construct(
        CollectionFactory $categoryCollectionFactory,
        StoreManagerInterface $storeManager,
        LoggerInterface $logger
    ) {
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
    }

    /**
     * Find a category from its exported path.
     *
     * Example:
     *
     * [
     *     'Shop',
     *     'Fragrance',
     *     'Men\'s Fragrances'
     * ]
     */
    public function findByPath(array $path): ?CategoryInterface
    {
        $parentId = (int)$this->storeManager
        ->getStore()
        ->getRootCategoryId(); // Magento Root Catalog

        $category = null;

        foreach ($path as $name) {

            $category = $this->findChildCategory(
                $parentId,
                $name
            );

            if (!$category) {

                $this->logger->warning(
                    sprintf(
                        'Unable to locate category "%s" beneath parent %d',
                        $name,
                        $parentId
                    )
                );

                return null;
            }

            $parentId = (int)$category->getId();
        }

        return $category;
    }


    /**
     * Finds the deepest existing category in a path.
     *
     * @param array $path
     * @return array
     */
    public function findDeepestExistingPath(array $path): array
    {
        $parent = null;
        $missing = [];
        $currentPath = [];

        foreach ($path as $index => $name) {

            $currentPath[] = $name;

            $category = $this->findByPath($currentPath);

            if ($category === null) {

                $missing = array_slice($path, $index);

                break;
            }

            $parent = $category;
        }

        return [
            'parent'   => $parent,
            'missing'  => $missing,
            'complete' => empty($missing)
        ];
    }


    /**
     * Find a direct child category beneath a parent.
     */
    private function findChildCategory(
        int $parentId,
        string $name
    ): ?CategoryInterface {

        $collection = $this->categoryCollectionFactory->create();

        $collection
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('name', $name)
            ->addAttributeToFilter('parent_id', $parentId)
            ->setPageSize(1);

        $category = $collection->getFirstItem();

        if (!$category->getId()) {
            return null;
        }

        return $category;
    }
}