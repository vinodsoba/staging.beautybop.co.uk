<?php

declare(strict_types=1);

namespace BeautyFort\BeautyfortProductImport\Model;

use Psr\Log\LoggerInterface;

class HighResImageService
{
    /**
     * @var WebsiteLogin
     */
    private $websiteLogin;

    /**
     * @var WebsiteSearch
     */
    private $websiteSearch;

    /**
     * @var PreviewParser
     */
    private $previewParser;


    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        WebsiteLogin $websiteLogin,
        WebsiteSearch $websiteSearch,
        PreviewParser $previewParser,
        LoggerInterface $logger
    ) {
        $this->websiteLogin = $websiteLogin;
        $this->websiteSearch = $websiteSearch;
        $this->previewParser = $previewParser;
        $this->logger = $logger;
    }

    /**
     * Returns the downloaded high-resolution image.
     */
    public function getImageUrlForSku(string $sku): ?string
    {
        $this->logger->info('HIGH RES LOOKUP START', [
            'sku' => $sku
        ]);

        if (!$this->websiteLogin->login()) {

            $this->logger->info('HIGH RES LOGIN FAILED', [
                'sku' => $sku
            ]);

            return null;
        }

        $this->logger->info('HIGH RES LOGIN OK', [
            'sku' => $sku
        ]);

        $previewId = $this->websiteSearch->findPreviewId($sku);

        $this->logger->info('HIGH RES PREVIEW ID', [
            'sku' => $sku,
            'preview_id' => $previewId
        ]);

        if (!$previewId) {
            return null;
        }

        $imageUrl = $this->previewParser->getImageUrl($previewId);

        $this->logger->info('HIGH RES IMAGE URL', [
            'sku' => $sku,
            'image_url' => $imageUrl
        ]);

        if (!$imageUrl) {
            return null;
        }

        return $imageUrl;
    }
}