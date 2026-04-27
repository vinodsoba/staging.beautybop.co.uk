<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */
namespace Aitoc\TwoFactorAuthentication\Model\ResourceModel;

class User extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Role constructor.
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param null                                              $connectionName
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('aitoc_twofactorauthentication_user', 'user_id');
    }
}
