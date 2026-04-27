<?php
namespace Magecomp\Inventoryupdate\Helper;

use Magento\Store\Model\ScopeInterface;
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const INVENTORYUPDATE_GENERAL_ENABLES = 'inventoryupdate/general/enable';
    const INVENTORYUPDATE_GENERAL_USERNAME = 'inventoryupdate/general/username';
    const INVENTORYUPDATE_GENERAL_PASSWORD = 'inventoryupdate/general/password';
    const INVENTORYUPDATE_GENERAL_SCHEMA_API_URL = 'inventoryupdate/general/schema_location_api_url';
    const INVENTORYUPDATE_GENERAL_ENDPOINT_API_URL = 'inventoryupdate/general/endpoints_api_url';
    const INVENTORYUPDATE_GENERAL_TESTMODE = 'inventoryupdate/general/testmode';

    protected $_storeManager;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    )
    {
        $this->_storeManager = $storeManager;
        parent::__construct($context);
    }

    public function getStoreid()
    {
        return $this->_storeManager->getStore()->getId();
    }

    public function isEnabled()
    {
        return $this->scopeConfig->getValue(self::INVENTORYUPDATE_GENERAL_ENABLES, ScopeInterface::SCOPE_STORE,$this->getStoreid());
    }

    public function isProductImportEnabled()
    {
        return (bool) ($this->scopeConfig->getValue('beautyfort/product_import_enabled'));
    }


    public function Apiusername()
    {
        if ($this->isEnabled()) {
            return $this->scopeConfig->getValue(self::INVENTORYUPDATE_GENERAL_USERNAME, ScopeInterface::SCOPE_STORE, $this->getStoreid());
        }
    }

    public function Apipassword()
    {
        if ($this->isEnabled()) {
            return $this->scopeConfig->getValue(self::INVENTORYUPDATE_GENERAL_PASSWORD, ScopeInterface::SCOPE_STORE, $this->getStoreid());
        }
    }

    public function Schemalocationapiurl()
    {
        if ($this->isEnabled()) {
            return $this->scopeConfig->getValue(self::INVENTORYUPDATE_GENERAL_SCHEMA_API_URL, ScopeInterface::SCOPE_STORE, $this->getStoreid());
        }
    }

    public function Endpointsapiurl()
    {
        if ($this->isEnabled()) {
            return $this->scopeConfig->getValue(self::INVENTORYUPDATE_GENERAL_ENDPOINT_API_URL, ScopeInterface::SCOPE_STORE, $this->getStoreid());
        }
    }
    public function Testmodecheck()
    {
        if ($this->isEnabled()) {
            return $this->scopeConfig->getValue(self::INVENTORYUPDATE_GENERAL_TESTMODE, ScopeInterface::SCOPE_STORE, $this->getStoreid());
        }
    }
}