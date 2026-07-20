<?php
declare(strict_types=1);

namespace BeautyBop\Core\Model\Seo;

use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;

class CategorySeoExporter
{
    /** @var CollectionFactory */
    private $categoryCollectionFactory;

    public function __construct(
        CollectionFactory $categoryCollectionFactory
    ) {
        $this->categoryCollectionFactory = $categoryCollectionFactory;
    }

    // Private helper methods


    private function getCategoryPathArray($category): array
    {
        $path = [];

        foreach ($category->getParentCategories() as $parent) {

            if ($parent->getLevel() <= 1) {
                continue;
            }

            $path[] = $parent->getName();
        }

        return $path;
    }


    public function execute(): array
    {
        $collection = $this->categoryCollectionFactory->create();

        $collection->addAttributeToSelect([
            'name',
            'url_key',
            'description',
            'meta_title',
            'meta_description',
            'meta_keywords'
        ]);

        $collection->addAttributeToFilter('level', ['gt' => 1]);
        $collection->addAttributeToFilter('is_active', 1);

        $data = [];

        foreach ($collection as $category) {
           $data[] = [
            'path' => $this->getCategoryPathArray($category),

            'url_key'          => $category->getUrlKey(),
            'name'             => $category->getName(),

            'description'      => $category->getDescription(),
            'meta_title'       => $category->getMetaTitle(),
            'meta_description' => $category->getMetaDescription(),
            'meta_keywords'    => $category->getMetaKeywords(),
        ];
        }

        $exportDir = BP . '/var/export';

        if (!is_dir($exportDir)) {
            mkdir($exportDir, 0755, true);
        }

        $file = $exportDir . '/seo-sync.json';

        file_put_contents(
            $file,
            json_encode(
                [
                    'version'      => '1.0',
                    'site'         => 'beautybop.co.uk',
                    'generated_at' => date('c'),
                    'categories'   => $data
                ],
                JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
            )
        );

        return [
            'file'       => $file,
            'categories' => count($data)
        ];
    }
}