<?php
declare(strict_types=1);

namespace BeautyBop\Core\Block\Product\View;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class ExploreMore extends Template
{
    private const MAX_LINKS = 4;

    private Registry $registry;
    private CategoryRepositoryInterface $categoryRepository;

    public function __construct(
        Context $context,
        Registry $registry,
        CategoryRepositoryInterface $categoryRepository,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->registry = $registry;
        $this->categoryRepository = $categoryRepository;
    }

    public function getProduct(): ?Product
    {
        $product = $this->registry->registry('current_product');

        return $product instanceof Product ? $product : null;
    }

    /**
     * Return useful active categories assigned to the current product.
     *
     * More-specific/deeper categories are shown first.
     */
    public function getExploreCategories(): array
    {
        $product = $this->getProduct();

        if (!$product) {
            return [];
        }

        $categories = [];

        foreach ($product->getCategoryIds() as $categoryId) {
            try {
                $category = $this->categoryRepository->get(
                    (int)$categoryId,
                    (int)$this->_storeManager->getStore()->getId()
                );

                if (!$category->getIsActive()) {
                    continue;
                }

                $categories[] = $category;
            } catch (\Exception $e) {
                continue;
            }
        }

        usort(
            $categories,
            static function ($a, $b): int {
                return (int)$b->getLevel() <=> (int)$a->getLevel();
            }
        );

        return array_slice($categories, 0, self::MAX_LINKS);
    }

}
