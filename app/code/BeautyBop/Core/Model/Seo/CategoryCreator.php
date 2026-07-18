<?php

declare(strict_types=1);

namespace BeautyBop\Core\Model\Seo;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class CategoryCreator
{
    private CategoryRepositoryInterface $categoryRepository;
    private CategoryFactory $categoryFactory;
    private StoreManagerInterface $storeManager;
    private CategoryLocator $categoryLocator;
    private LoggerInterface $logger;

    public function __construct(
        CategoryRepositoryInterface $categoryRepository,
        CategoryFactory $categoryFactory,
        StoreManagerInterface $storeManager,
        CategoryLocator $categoryLocator,
        LoggerInterface $logger
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->categoryFactory = $categoryFactory;
        $this->storeManager = $storeManager;
        $this->categoryLocator = $categoryLocator;
        $this->logger = $logger;
    }

    /**
     * Create missing categories.
     *
     * @param array $missingCategories
     * @return array
     */
    public function create(array $missingCategories): array
    {
        $created = [];

        foreach ($missingCategories as $category) {

           $analysis = $this->categoryLocator->findDeepestExistingPath(
            $category['path']
            );

            $this->logger->info(
                'Category creation analysis.',
                [
                    'path' => $category['path'],
                    'parent' => $analysis['parent']
                        ? $analysis['parent']->getName()
                        : 'None',
                    'missing' => $analysis['missing']
                ]
            );

            /*
             * Actual category creation
             * comes in Version 2.
             */

            $created[] = $category;
        }

        return $created;
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
            'parent'  => $parent,
            'missing' => $missing
        ];
    }
}