<?php
/**
 * Copyright © Ulmod. All rights reserved.
 * See LICENSE.txt for license details.
 */
 
namespace Ulmod\NewsletterPopup\Block\Backend;

use Magento\Framework\Registry;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Cms\Model\Wysiwyg\Config as WysiwygConfig;

class Editor extends \Magento\Config\Block\System\Config\Form\Field
{
    protected $wysiwygConfig;

    /**
     * @param Context $context
     * @param WysiwygConfig $wysiwygConfig
     * @param array $data
     */
    public function __construct(
        Context $context,
        WysiwygConfig $wysiwygConfig,
        array $data = []
    ) {
        $this->wysiwygConfig = $wysiwygConfig;
        parent::__construct($context, $data);
    }

    /**
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $element->setWysiwyg(true);
        $element->setConfig($this->wysiwygConfig->getConfig($element));
        
        return parent::_getElementHtml($element);
    }
}
