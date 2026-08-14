<?php
/**
 * Copyright © Ulmod. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Ulmod\NewsletterPopup\Controller\Subscriber;

use Magento\Framework\HTTP\Client\Curl;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Model\Url as CustomerUrl;
use Magento\Newsletter\Model\SubscriberFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Customer\Api\AccountManagementInterface as CustomerAccountManagement;
use Magento\Newsletter\Model\Subscriber as SubscriberModel;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Validator\EmailAddress;



class NewAction extends Action
{
    /**
    * @var Curl
    */
    protected $curl;

    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var CustomerAccountManagement
     */
    protected $customerAccountManagement;
    
    /**
     * @var SubscriberFactory
     */
    protected $subscriberFactory;

    /**
     * @var CustomerUrl
     */
    protected $customerUrl;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;
    
    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var EmailAddress
     */
    protected $emailValidator;
    
    /**
     * @param Context $context
     * @param SubscriberFactory $subscriberFactory
     * @param Session $customerSession
     * @param StoreManagerInterface $storeManager
     * @param CustomerUrl $customerUrl
     * @param CustomerAccountManagement $customerAccountManagement
     * @param JsonFactory $resultJsonFactory
     */
    public function __construct(
        Context $context,
        SubscriberFactory $subscriberFactory,
        Session $customerSession,
        StoreManagerInterface $storeManager,
        CustomerUrl $customerUrl,
        CustomerAccountManagement $customerAccountManagement,
        JsonFactory $resultJsonFactory,
        ScopeConfigInterface $scopeConfig,
        Curl $curl,
        EmailAddress $emailValidator
    ) {
        $this->customerAccountManagement = $customerAccountManagement;
        $this->storeManager = $storeManager;
        $this->subscriberFactory = $subscriberFactory;
        $this->customerSession = $customerSession;
        $this->customerUrl = $customerUrl;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->scopeConfig = $scopeConfig;
        $this->curl = $curl;
        $this->emailValidator = $emailValidator;
        parent::__construct($context);
    }

    /**
     * Validates the format of the email address
     *
     * @param string $email
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return void
     */
    
    protected function validateEmailFormat($email)
    {
        if (!$this->emailValidator->isValid($email)) {
            throw new LocalizedException(
                __('Please enter a valid email address.')
            );
        }
    }
    
    /**
     * Validates that if the current user is a guest,
     * that they can subscribe to a newsletter.
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return void
     */
    protected function validateGuestSubscription()
    {
        $isGuestAllowed = $this->scopeConfig->getValue(
            SubscriberModel::XML_PATH_ALLOW_GUEST_SUBSCRIBE_FLAG,
            ScopeInterface::SCOPE_STORE
        );
        
        if ($isGuestAllowed != 1 && !$this->customerSession->isLoggedIn()) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __(
                    'Sorry, but the admin denied subscription for guests. Please <a href="%1">register</a>.',
                    $this->customerUrl->getRegisterUrl()
                )
            );
        }
    }

    /**
     * Validates that the email address isn't being used by a different account.
     *
     * @param string $email
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return void
     */
    protected function validateEmailAvailable($email)
    {
        $websiteId = $this->storeManager->getStore()->getWebsiteId();
        if ($this->customerSession->getCustomerDataObject()->getEmail() !== $email
            && !$this->customerAccountManagement->isEmailAvailable($email, $websiteId)
        ) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('This email address is already assigned to another user.')
            );
        }
    }

    /**
     * New subscription action
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return void
     */
    public function execute()
    {
        $data['error'] = true;
        $data['message'] = __('Please enter your email address.');

        $isAjax = $this->getRequest()->isAjax();
        $methodRequest = $this->getRequest()->getMethod();
        $isXmlHttpRequest = $this->getRequest()->isXmlHttpRequest();
        $emailPost = $this->getRequest()->getPost('email');

        if ($isAjax && $methodRequest == 'POST'
            && $isXmlHttpRequest && $emailPost
        ) {
            $subscribedEmail = (string)$emailPost;
            try {
                $this->validateEmailFormat($subscribedEmail);
                $this->validateEmailAvailable($subscribedEmail);
                $this->validateGuestSubscription();
                
                $this->validateRecaptcha();
                
                $subscribeStatus = $this->subscriberFactory->create()
                    ->subscribe($subscribedEmail);
                    
                $data['error'] = false;
                if ($subscribeStatus == SubscriberModel::STATUS_NOT_ACTIVE) {
                    $data['message'] = __('The confirmation request has been sent.');
                } else {
                    $data['message'] = __('Thank you for your subscription.');
                }
            } catch (LocalizedException $e) {
                $data['message'] = __(
                    'There was a problem with the subscription: %1',
                    $e->getMessage()
                );
            } catch (\Exception $e) {
                $data['message'] = $e->getMessage();
            }
        }

        $result = $this->resultJsonFactory->create();
        
        return $result->setData($data);
    }

    /**
     * Validate Google reCAPTCHA
     *
     * @throws LocalizedException
     */
    protected function validateRecaptcha()
    {
        $captchaResponse = $this->getRequest()->getParam('g-recaptcha-response');

        if (!$captchaResponse) {
            throw new LocalizedException(
                __('Please complete the reCAPTCHA.')
            );
        }

        $secretKey = '6LfDxKUUAAAAAPLz3899pbRxRmM3rl4ehanJ4_8B';

        $this->curl->post(
            'https://www.google.com/recaptcha/api/siteverify',
            [
                'secret' => $secretKey,
                'response' => $captchaResponse,
                'remoteip' => $_SERVER['REMOTE_ADDR']
            ]
        );

        $result = json_decode($this->curl->getBody(), true);

        if (
            empty($result)
            || empty($result['success'])
            || !$result['success']
        ) {
            throw new LocalizedException(
                __('Captcha validation failed.')
            );
        }
    }
}
