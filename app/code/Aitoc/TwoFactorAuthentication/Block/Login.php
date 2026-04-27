<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */
namespace Aitoc\TwoFactorAuthentication\Block;

class Login extends \Magento\Framework\View\Element\Template
{
    /**
     * @var string
     */
    protected $_template = 'default.phtml';

    /**
     * @var \Aitoc\TwoFactorAuthentication\Model\Authentication
     */
    protected $authModel;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Aitoc\TwoFactorAuthentication\Model\Authentication $authModel
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Aitoc\TwoFactorAuthentication\Model\Authentication $authModel,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->authModel = $authModel;
    }

    /**
     * Returns template
     *
     * @return string
     */
    public function getTemplate()
    {
        return $this->getIsAjax() ? '' : $this->_template;
    }

    /**
     * Renders HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->authModel->isEnabled()) {
            return parent::_toHtml();
        }
        return '';
    }
}
