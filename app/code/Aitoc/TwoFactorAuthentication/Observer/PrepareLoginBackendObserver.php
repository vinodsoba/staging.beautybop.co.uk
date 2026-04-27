<?php
namespace Aitoc\TwoFactorAuthentication\Observer;

use Magento\Framework\Event\ObserverInterface;

class PrepareLoginBackendObserver implements ObserverInterface
{
    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $authStorage;

    /**
     * @param \Magento\Backend\Model\Auth\Session $authStorage
     */
    public function __construct(
        \Magento\Backend\Model\Auth\Session $authStorage
    ) {
        $this->authStorage = $authStorage;
    }

    /**
     * Prepare Login Backend
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $this->authStorage->setAllowBackendAccountLoginCheck(true);
    }
}
