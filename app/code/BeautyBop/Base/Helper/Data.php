<?php

namespace BeautyBop\Base\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Data extends AbstractHelper
{
    public function getProductRating($product)
    {
        $id = $product->getId();

        $ratings = [
            ['stars' => '★★★★★', 'score' => '4.8', 'count' => 132],
            ['stars' => '★★★★☆', 'score' => '4.5', 'count' => 98],
            ['stars' => '★★★★☆', 'score' => '4.3', 'count' => 76],
            ['stars' => '★★★★★', 'score' => '4.7', 'count' => 154],
        ];

        // Deterministic (same product always same rating)
        return $ratings[$id % count($ratings)];
    }
}