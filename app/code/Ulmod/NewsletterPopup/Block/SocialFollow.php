<?php
/**
 * Copyright © Ulmod. All rights reserved.
 * See LICENSE.txt for license details.
 */
 
namespace Ulmod\NewsletterPopup\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Ulmod\NewsletterPopup\Model\Config as ModelConfig;

class SocialFollow extends Template
{
    /**
     * @var ModelConfig
     */
    protected $modelConfig;
    
    /**
     * @param   Context $context
     * @param   ModelConfig $modelConfig
     * @param   array $data
     */
    public function __construct(
        Context $context,
        ModelConfig $modelConfig,
        array $data = []
    ) {
        $this->modelConfig = $modelConfig;
        parent::__construct($context, $data);
    }
    
    /**
     * Get config model
     *
     * @return ModelConfig
     */
    public function getConfigModel()
    {
        return $this->modelConfig;
    }
}
