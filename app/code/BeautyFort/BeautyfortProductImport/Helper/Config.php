<?php
namespace BeautyFort\BeautyfortProductImport\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Config extends AbstractHelper
{
    /**
     * Matches:
     * section: beautyfort_v4
     * group:   api
     */
    private const XML_PATH = 'beautyfort_v4/api/';

    /**
     * WSDL URL
     */
    public function getWsdl(): ?string
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH . 'wsdl',
            ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * SOAP endpoint
     */
    public function getEndpoint(): ?string
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH . 'endpoint',
            ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * API username
     */
    public function getUsername(): ?string
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH . 'username',
            ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * API password
     */
    public function getPassword(): ?string
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH . 'password',
            ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * Test mode flag
     */
    public function isTestMode(): bool
    {
        return (bool) $this->scopeConfig->getValue(
            self::XML_PATH . 'test_mode',
            ScopeInterface::SCOPE_WEBSITE
        );
    }
}
