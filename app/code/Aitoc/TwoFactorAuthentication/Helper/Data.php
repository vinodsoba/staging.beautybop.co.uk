<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */
namespace Aitoc\TwoFactorAuthentication\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var string
     */
    const LOGIN_FIELD_NAME = 'otp_password';
    
    /**
     * @var string
     */
    const OTP_EMAIL_TEMPLATE = 'otp_email';
}
