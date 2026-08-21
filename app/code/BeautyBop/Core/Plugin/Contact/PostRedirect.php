<?php

declare(strict_types=1);

namespace BeautyBop\Core\Plugin\Contact;

use Magento\Contact\Controller\Index\Post;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\UrlInterface;

class PostRedirect
{
    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    public function __construct(
        UrlInterface $urlBuilder
    ) {
        $this->urlBuilder = $urlBuilder;
    }

    public function afterExecute(
        Post $subject,
        $result
    ) {
        if ($result instanceof Redirect) {
            $result->setUrl(
                $this->urlBuilder->getUrl('', [
                    '_direct' => 'contact.html'
                ])
            );
        }

        return $result;
    }
}