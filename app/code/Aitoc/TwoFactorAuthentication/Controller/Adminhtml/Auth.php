<?php
namespace Aitoc\TwoFactorAuthentication\Controller\Adminhtml;

/**
 *  Auth controller
 */
abstract class Auth extends \Magento\Backend\App\AbstractAction
{
    /**
     * User model factory
     *
     * @var \Magento\User\Model\UserFactory
     */
    protected $userFactory;

    /**
     * @var \Aitoc\TwoFactorAuthentication\Model\Authentication
     */
    protected $authModel;
    
    /**
     * @var \Aitoc\TwoFactorAuthentication\Model\Ip
     */
    protected $ipModel;

    /**
     * @var \Aitoc\TwoFactorAuthentication\Model\User
     */
    protected $user;
    
    /**
     * Construct
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\User\Model\UserFactory $userFactory
     * @param \Aitoc\TwoFactorAuthentication\Model\Authentication $authModel
     * @param \Aitoc\TwoFactorAuthentication\Model\Ip $ipModel
     * @param \Aitoc\TwoFactorAuthentication\Model\User $user
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\User\Model\UserFactory $userFactory,
        \Aitoc\TwoFactorAuthentication\Model\Authentication $authModel,
        \Aitoc\TwoFactorAuthentication\Model\Ip $ipModel,
        \Aitoc\TwoFactorAuthentication\Model\User $user
    ) {
        parent::__construct($context);
        $this->userFactory = $userFactory;
        $this->authModel = $authModel;
        $this->ipModel = $ipModel;
        $this->user = $user;
    }
    
    /**
     * Check if user has permissions to access this controller
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return true;
    }
}
