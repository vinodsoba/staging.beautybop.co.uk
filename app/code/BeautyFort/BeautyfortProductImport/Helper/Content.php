<?php
declare(strict_types=1);

namespace BeautyFort\BeautyfortProductImport\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Content extends AbstractHelper
{
    public function buildMetaTitle(string $name): string
    {
        return sprintf('%s | Buy Online UK | BeautyBop', $name);
    }

    public function buildMetaDescription(string $name): string
    {
        return sprintf(
            'Buy %s online at BeautyBop. Authentic designer fragrance with fast UK delivery and secure checkout.',
            $name
        );
    }

    public function buildMetaKeywords(string $name): string
    {
        return strtolower(str_replace(' ', ',', $name)) . ',designer fragrance,perfume uk,buy fragrance online';
    }

    public function buildShortDescription(string $name): string
    {
        return sprintf(
            'Shop %s at BeautyBop. A premium designer fragrance with a distinctive scent and long-lasting performance. Fast UK delivery available.',
            $name
        );
    }

    public function buildDescription($apiProduct): string
    {
        $name = $apiProduct->Name ?? '';
        $brand = $apiProduct->Brand ?? '';
        $brandSlug = trim(strtolower(preg_replace('/[^a-z0-9]+/', '-', $brand)), '-');
        $brandUrl = "/brands/{$brandSlug}-fragrance.html";

        if (!empty($apiProduct->Description)) {
            return sprintf(
                '<p><strong>%s</strong> by <a href="%s">%s</a>.</p>
                 <p>%s</p>
                 <p>Buy <strong>%s</strong> online at BeautyBop with fast UK delivery.</p>',
                $name,
                $brandUrl,
                $brand,
                $apiProduct->Description,
                $name
            );
        }

        return sprintf(
            '<p><strong>%s</strong> is a premium fragrance from <a href="%s">%s</a> known for its distinctive scent and long lasting performance.</p>
             <p>Perfect for everyday wear or special occasions, this designer fragrance offers elegance and sophistication.</p>
             <p>Buy <strong>%s</strong> online at BeautyBop. Authentic fragrances with secure checkout and fast UK delivery.</p>',
            $name,
            $brandUrl,
            $brand,
            $name
        );
    }
}