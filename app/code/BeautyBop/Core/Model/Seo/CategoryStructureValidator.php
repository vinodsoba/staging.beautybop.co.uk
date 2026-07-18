<?php

declare(strict_types=1);

namespace BeautyBop\Core\Model\Seo;

class CategoryStructureValidator
{
    /**
     * @var CategoryLocator
     */
    private CategoryLocator $categoryLocator;

    public function __construct(
        CategoryLocator $categoryLocator
    ) {
        $this->categoryLocator = $categoryLocator;
    }

    /**
     * Validate exported category structure.
     *
     * @param array $categories
     * @return array
     */
    public function validate(array $categories): array
    {
        $report = [
            'valid'   => true,
            'total'   => count($categories),
            'found'   => 0,
            'checked' => [],
            'missing' => []
        ];

        foreach ($categories as $category) {

            $result = $this->categoryLocator->findByPath(
                $category['path']
            );

            if ($result) {

                $report['found']++;

                continue;
            }

            $report['valid'] = false;

            $report['missing'][] = $category;

            $report['checked'][] = [
                'path' => $category['path'],
                'status' => $result ? 'found' : 'missing'
            ];
        }

        return $report;
    }
}