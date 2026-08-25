<?php

declare(strict_types=1);

namespace BeautyBop\Core\Block\Contact;

use Magento\Contact\Block\ContactForm;
use Magento\ReCaptchaUi\Block\ReCaptcha;

class Form extends ContactForm
{
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $recaptchaType = (string)$this->_scopeConfig->getValue(
            'recaptcha_frontend/type_for/contact'
        );

        if (!$recaptchaType) {
            return $this;
        }

        /** @var ReCaptcha $recaptcha */
        $recaptcha = $this->getLayout()->createBlock(
            ReCaptcha::class,
            'beautybop.contact.recaptcha',
            [
                'data' => [
                    'recaptcha_for' => 'contact',
                    'jsLayout' => [
                        'components' => [
                            'recaptcha' => [
                                'component' =>
                                    'Magento_ReCaptchaFrontendUi/js/reCaptcha'
                            ]
                        ]
                    ]
                ]
            ]
        );

        if (!$recaptcha) {
            return $this;
        }

        $recaptcha->setTemplate(
            'Magento_ReCaptchaFrontendUi::recaptcha.phtml'
        );

        $this->setChild(
            'form.additional.info',
            $recaptcha
        );

        return $this;
    }
}