<?php

declare(strict_types=1);

namespace BeautyFort\BeautyfortProductImport\Model;

use BeautyFort\BeautyfortProductImport\Helper\Config;
use Psr\Log\LoggerInterface;

class WebsiteLogin
{
    private const LOGIN_URL = 'https://www.beautyfort.com/';

    private Config $config;
    private LoggerInterface $logger;

    public function __construct(
        Config $config,
        LoggerInterface $logger
    ) {
        $this->config = $config;
        $this->logger = $logger;
    }

    /**
     * Attempt to log into the BeautyFort website.
     */
    public function login(): bool
    {
        $cookieFile = BP . '/var/beautyfort.cookies';

        /*
         * STEP 1
         * Visit homepage first to establish a session
         */
        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL            => self::LOGIN_URL,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_COOKIEJAR      => $cookieFile,
            CURLOPT_COOKIEFILE     => $cookieFile,
            CURLOPT_USERAGENT      => 'Mozilla/5.0',
            CURLOPT_TIMEOUT        => 30,
        ]);

        $homepage = curl_exec($ch);

        $this->logger->info('BeautyFort Homepage', [
            'http_code' => curl_getinfo($ch, CURLINFO_HTTP_CODE),
            'url'       => curl_getinfo($ch, CURLINFO_EFFECTIVE_URL)
        ]);

        file_put_contents(
            BP . '/var/log/beautyfort-homepage.html',
            $homepage
        );

        curl_close($ch);

        /*
         * STEP 2
         * Submit login using same session
         */
        $postFields = [
            'Email'    => $this->config->getWebsiteEmail(),
            'Password' => $this->config->getWebsitePassword(),
            'action'   => 'login',
            's'        => 'LOGIN'
        ];

        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL            => self::LOGIN_URL,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query($postFields),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_COOKIEJAR      => $cookieFile,
            CURLOPT_COOKIEFILE     => $cookieFile,
            CURLOPT_USERAGENT      => 'Mozilla/5.0',
            CURLOPT_TIMEOUT        => 30,

            CURLOPT_HTTPHEADER => [
                'Origin: https://www.beautyfort.com',
                'Referer: https://www.beautyfort.com/',
                'Content-Type: application/x-www-form-urlencoded'
            ]
        ]);

        $response = curl_exec($ch);

        if ($response === false) {

            $this->logger->error('BeautyFort Website Login Failed', [
                'error' => curl_error($ch)
            ]);

            curl_close($ch);

            return false;
        }

        $httpCode     = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $effectiveUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        $redirectUrl  = curl_getinfo($ch, CURLINFO_REDIRECT_URL);
        $curlError    = curl_error($ch);

        file_put_contents(
            BP . '/var/log/beautyfort-login-response.html',
            $response
        );

        $this->logger->info('BeautyFort Website Login Debug', [
            'http_code'     => $httpCode,
            'effective_url' => $effectiveUrl,
            'redirect_url'  => $redirectUrl,
            'curl_error'    => $curlError,
        ]);

        curl_close($ch);

        /*
         * TEMPORARY:
         * Until we know how BeautyFort responds,
         * don't assume success is only a 302.
         */
        if (
            stripos($response, 'Invalid login details') !== false
        ) {
            return false;
        }

        return true;
    }
}