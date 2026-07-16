<?php

namespace BeautyFort\BeautyfortProductImport\Helper;

use BeautyFort\BeautyfortProductImport\Helper\Config;
use BeautyFort\BeautyfortProductImport\Logger\Logger;

class Api
{
    /** @var Logger */
    private $logger;

    /** @var Config */
    private $config;

    /** @var \SoapClient|null */
    private $client = null;

    /** @var array */
    private $cache = [];

    public function __construct(
        Logger $logger,
        Config $config
    ) {
        $this->logger = $logger;
        $this->config = $config;
    }

    /**
     * Lazily create SOAP client (V2)
     */
    public function getClient(): \SoapClient
    {
  
    $wsdl = 'http://www.beautyfort.com/api/wsdl/v2/wsdl.wsdl';
    $endpoint = 'http://www.beautyfort.com/api/soap/';

    $client = new \SoapClient($wsdl, [
        'location'   => $endpoint,
        'exceptions' => true,
        'trace'      => false
    ]);


    $client->__setSoapHeaders($this->buildAuthHeader());


    return $client;
    }


    /**
     * Build V2 auth header
     */
    private function buildAuthHeader(): \SoapHeader
    {
        $username = $this->config->getUsername();
        $password = $this->config->getPassword();

        $created = gmdate('Y-m-d\TH:i:s\Z');
        $nonce   = substr(md5(uniqid($password, true)), 0, 16);
        $digest  = base64_encode($nonce);

        $auth = new \stdClass();
        $auth->Username = $username;
        $auth->Nonce    = $digest;
        $auth->Created  = $created;
        $auth->Password = base64_encode(sha1($auth->Nonce . $auth->Created . $password));

        return new \SoapHeader(
            'http://www.beautyfort.com/api/',
            'AuthHeader',
            $auth,
            false
        );
    }

    /**
     * Search product by SKU (Admin)
     */
    public function fetchProductBySku(string $sku): array
    {
        $sku = trim($sku);

        $this->logger->info('🧪 STEP 4.4 — fetchProductBySku()', ['sku' => $sku]);

        if ($sku === '') {
            return [];
        }


        /** ✅ Cache hit */
        if (isset($this->cache[$sku])) {
            $this->logger->info('🟢 CACHE HIT', ['sku' => $sku]);
            return $this->cache[$sku];
        }

        try {
            $client = $this->getClient();

            $request = new \stdClass();
            $request->SearchTerm = $sku;
            $request->TestMode= false; 
            $request->Page = 1;
            $request->ResultsPerPage = 5;

            $this->logger->info('📡 Calling ProductSearch');

            $response = $client->ProductSearch($request);

            $this->logger->info(
                'SOAP RAW RESPONSE',
                ['response' => print_r($response, true)]
            );

            $this->logger->info('📨 SOAP response received');

            $items = $this->extractItems($response);

            $this->logger->info('📦 Products returned', [
                'sku'   => $sku,
                'count' => count($items)
            ]);

            $this->cache[$sku] = $items;

            $this->logger->info('💾 Cached supplier response', [
                'sku' => $sku
            ]);

            return $items;

        } catch (\SoapFault $e) {
            $this->logger->error('❌ SOAP FAULT', [
                'sku'   => $sku,
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }


    /**
     * Fetch ALL supplier products in bulk (paged)
     */
    public function fetchAllProducts(): array
    {
        $this->logger->info('🚀 Bulk Product Fetch Started');

        $allProducts = [];
        $page = 1;
        $resultsPerPage = 100; // adjust if supplier allows more

        try {

            do {

                $this->logger->info('DEBUG: About to create SOAP client');

                $client = $this->getClient();
                
                $this->logger->info('DEBUG: SOAP client created');

                $request = new \stdClass();
                $request->SearchTerm = ''; // empty = return all
                $request->TestMode = false;
                $request->Page = $page;
                $request->ResultsPerPage = $resultsPerPage;

                $this->logger->info('📡 ProductSearch bulk call', [
                    'page' => $page
                ]);

                $response = $client->ProductSearch($request);

                $items = $this->extractItems($response);

                if (empty($items)) {
                    break;
                }

                foreach ($items as $item) {
                    if (isset($item->SKU)) {
                        $allProducts[$item->SKU] = $item;
                    }
                }

                $page++;

            } while (count($items) === $resultsPerPage);

            $this->logger->info('✅ Bulk fetch complete', [
                'total_products' => count($allProducts)
            ]);

            return $allProducts;

        } catch (\SoapFault $e) {

            $this->logger->error('❌ BULK SOAP FAULT', [
                'error' => $e->getMessage()
            ]);

            return [];
        }
    }

    /**
     * Normalise V2 response
     */
    private function extractItems($response): array
    {
        if (!isset($response->Items->Item)) {
            $this->logger->warning('⚠️ No Items->Item in response');
            return [];
        }

        return is_array($response->Items->Item)
            ? $response->Items->Item
            : [$response->Items->Item];
    }

    public function fetchProductsByBrand(string $brand): array
    {
        $brand = trim($brand);

        $this->logger->info('🧪 Brand search started', [
            'brand' => $brand
        ]);

        if ($brand === '') {
            return [];
        }

        $results = [];
        $page = 1;
        $resultsPerPage = 10;

        try {

            do {

                $client = $this->getClient();

                $request = new \stdClass();
                $request->SearchTerm = $brand;
                $request->TestMode = false;
                $request->Page = $page;
                $request->ResultsPerPage = $resultsPerPage;

                $this->logger->info('📡 Brand ProductSearch call', [
                    'page' => $page
                ]);

                $response = $client->ProductSearch($request);

                $items = $this->extractItems($response);

                if (empty($items)) {
                    break;
                }

                foreach ($items as $item) {
                    $results[] = $item;
                }

                $page++;

            } while (count($items) === $resultsPerPage);

            $this->logger->info('✅ Brand API results', [
                'brand' => $brand,
                'count' => count($results)
            ]);

            return $results;

        } catch (\SoapFault $e) {

            $this->logger->error('❌ Brand SOAP FAULT', [
                'brand' => $brand,
                'error' => $e->getMessage()
            ]);

            return [];
        }
    }


    public function getStockFile(): array
    {
        $this->logger->info('📦 Starting GetStockFile');

        try {

            $client = $this->getClient();

            $ap_param['TestMode'] = false;
            $ap_param['StockFileFormat'] = 'JSON';
            $ap_param['FieldDelimiter'] = ',';
            $ap_param['StockFileFields']['StockFileField'] = [
                'Price',
                'RRP',
                'StockLevel'
            ];
            $ap_param['SortBy'] = 'StockLevel';

            $response = $client->GetStockFile($ap_param);

            $data = json_decode($response->File, true);

            $this->logger->info('✅ GetStockFile downloaded successfully', [
                'products' => count($data)
            ]);

            return $data;

            return json_decode($response->File, true);

        } catch (\Throwable $e) {

            $this->logger->error($e->getMessage());

            return [];
        }
    }
}
