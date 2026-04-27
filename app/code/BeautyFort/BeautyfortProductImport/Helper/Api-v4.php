<?php
namespace BeautyFort\BeautyfortProductImport\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use BeautyFort\BeautyfortProductImport\Helper\Config as V4Config;
use BeautyFort\BeautyfortProductImport\Logger\Logger;

class Api extends AbstractHelper
{
    protected $config;
    protected $logger;
    protected $soapClient;

    public function __construct(
        Context $context,
        V4Config $config,
        Logger $logger
    ) {
        parent::__construct($context);
        $this->config = $config;
        $this->logger = $logger;

        $this->logger->info('Beautyfort API helper loaded');
    }

    /* ============================================================
     * SOAP CLIENT
     * ============================================================ */

    protected function getSoapClient(): \SoapClient
    {
        if ($this->soapClient instanceof \SoapClient) {
            return $this->soapClient;
        }

        $this->logger->info('Beautyfort SOAP config', [
            'wsdl'     => $this->config->getWsdl(),
            'endpoint' => $this->config->getEndpoint()
        ]);

        $this->soapClient = new \SoapClient(
            $this->config->getWsdl(),
            [
                'location'   => $this->config->getEndpoint(),
                'uri'        => 'http://www.beautyfort.com/api/',
                'trace'      => 1,
                'exceptions' => true,
                'cache_wsdl' => WSDL_CACHE_NONE,
            ]
        );

        $this->logger->info('Beautyfort SOAP client initialised (v4)');

        return $this->soapClient;
    }

    /* ============================================================
     * AUTH HEADER
     * ============================================================ */

    private function buildAuthHeader(): \SoapHeader
    {
        $username = $this->config->getUsername();
        $password = $this->config->getPassword();

        // MUST be UTC Zulu
        $created = gmdate('Y-m-d\TH:i:s\Z');

        // RAW nonce bytes (this is critical)
        $nonceRaw = random_bytes(16);

        // Base64 nonce for transport
        $nonceB64 = base64_encode($nonceRaw);

        // Digest uses RAW nonce bytes
        $passwordDigest = base64_encode(
            sha1($nonceRaw . $created . $password, true)
        );

        $auth = new \stdClass();
        $auth->Username = $username;
        $auth->Nonce    = $nonceB64;
        $auth->Created  = $created;
        $auth->Password = $passwordDigest;

        return new \SoapHeader(
            'http://www.beautyfort.com/api/',
            'AuthHeader',
            $auth,
            false
        );
    }


    /* ============================================================
     * SOAP DEBUG LOGGER (CRITICAL)
     * ============================================================ */

    private function logSoapDebug(\SoapClient $client, string $context): void
    {
        try {
            $this->logger->info("SOAP REQUEST [$context]");
            $this->logger->info($client->__getLastRequest() ?: '[NO REQUEST]');

            $this->logger->info("SOAP RESPONSE [$context]");
            $this->logger->info($client->__getLastResponse() ?: '[NO RESPONSE]');
        } catch (\Throwable $t) {
            $this->logger->error('SOAP DEBUG FAILED', [
                'error' => $t->getMessage()
            ]);
        }
    }

    /* ============================================================
     * FETCH PRODUCTS (PAGINATED)
     * ============================================================ */

    public function fetchProducts(int $page = 1, int $limit = 10): array
    {
        $client = null;

        try {
            $client = $this->getSoapClient();

            // 🔥 ALWAYS re-attach header before call
            $client->__setSoapHeaders([$this->buildAuthHeader()]);

            $req = new \stdClass();
            $req->TestMode       = (bool) $this->config->isTestMode();
            $req->Page           = $page;
            $req->ResultsPerPage = $limit;

            $this->logger->info('Beautyfort ProductSearch', [
                'page'  => $page,
                'limit' => $limit,
                'test'  => $req->TestMode
            ]);

            $response = $client->ProductSearch($req);

            $this->logSoapDebug($client, 'ProductSearch');

            return $this->extractItems($response);

        } catch (\SoapFault $e) {

            if ($client) {
                $this->logSoapDebug($client, 'ProductSearch FAULT');
            }

            $this->logger->error('Beautyfort ProductSearch SOAP fault', [
                'message' => $e->getMessage()
            ]);

            return [];
        }
    }

    /* ============================================================
     * FETCH PRODUCT BY SKU
     * ============================================================ */

    public function fetchProductBySku(string $sku): array
    {
        $client = null;

        try {
            $client = $this->getSoapClient();
            $client->__setSoapHeaders([$this->buildAuthHeader()]);

            $req = new \stdClass();
            $req->TestMode       = (bool) $this->config->isTestMode();
            $req->Page           = 1;
            $req->ResultsPerPage = 1;

            $req->StockCodes = new \stdClass();
            $req->StockCodes->StockCode = [$sku];
            $req->SearchTerm = $sku;

            $this->logger->info('Beautyfort ProductSearch (SKU)', [
                'sku' => $sku
            ]);

            $response = $client->ProductSearch($req);

            $this->logSoapDebug($client, "ProductSearch SKU {$sku}");

            return $this->extractItems($response);

        } catch (\SoapFault $e) {

            if ($client) {
                $this->logSoapDebug($client, "ProductSearch SKU {$sku} FAULT");
            }

            $this->logger->error('Beautyfort ProductSearch SKU SOAP fault', [
                'sku'     => $sku,
                'message' => $e->getMessage()
            ]);

            return [];
        }
    }

    /* ============================================================
     * RESPONSE NORMALISATION
     * ============================================================ */

    protected function extractItems($response): array
    {
        if (!isset($response->Items) || !isset($response->Items->Item)) {
            return [];
        }

        $items = $response->Items->Item;

        return is_array($items) ? $items : [$items];
    }
}
