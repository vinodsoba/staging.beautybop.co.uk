<?php

namespace BeautyFort\BeautyfortProductImport\Plugin;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Page\Config;

class NoIndexFilters
{
    private $request;

    public function __construct(RequestInterface $request)
    {
        $this->request = $request;
    }

    public function afterGetRobots(\Magento\Framework\View\Page\Config $subject, $result)
    {
    $queryParams = $this->request->getQuery();

    // Only apply noindex if layered navigation filters exist
    $filterParams = ['price', 'bottle_size', 'color', 'fragrance_type', 'brand', 'cat'];

        foreach ($filterParams as $param) {
            if (isset($queryParams[$param])) {
                return 'NOINDEX,FOLLOW';
            }
        }

        return $result;
    }
}