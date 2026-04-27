<?php
/**
 * Copyright © Ulmod. All rights reserved.
 * See LICENSE.txt for license details.
 */
 
namespace Ulmod\NewsletterPopup\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class Behavior implements ArrayInterface
{
    const AFTER_PAGE_LOADED = 1;
    const AFTER_X_MSECONDS = 2;
    const AFTER_SCROLL_X = 3;
    
    /**
     * Options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::AFTER_PAGE_LOADED, 'label' => __('After Page Loaded')],
            ['value' => self::AFTER_X_MSECONDS, 'label' => __('After x Seconds')],
            ['value' => self::AFTER_SCROLL_X, 'label' => __('After Scrolled Down x% Of The Page')],
        ];
    }
}
