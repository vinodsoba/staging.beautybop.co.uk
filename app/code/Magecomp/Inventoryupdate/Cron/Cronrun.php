<?php
namespace Magecomp\Inventoryupdate\Cron;

use BeautyFort\BeautyfortProductImport\Helper\Api as BeautyfortApi;
use Magecomp\Inventoryupdate\Helper\Data;
use Magecomp\Inventoryupdate\Logger\Logger;
use \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use \Magento\Catalog\Model\ProductRepository;
class Cronrun
{
    protected $_logger;
    protected $helperinventoryupdate;
    protected $_productFactory;
    protected $_ProductRepository;
    protected $beautyfortApi;


    public function __construct(
        Data $helperinventoryupdate, 
        Logger $logger, 
        CollectionFactory $productFactory,
        ProductRepository $_ProductRepository,
        BeautyfortApi $beautyfortApi
    )
    {
        $this->helperinventoryupdate = $helperinventoryupdate;
        $this->_logger = $logger;
        $this->_productFactory = $productFactory;
        $this->_ProductRepository = $_ProductRepository;
        $this->beautyfortApi = $beautyfortApi;

    }

    public function execute()
    {

        file_put_contents(
        BP . '/var/log/cronrun_test.log',
        "Cronrun execute hit at " . date('Y-m-d H:i:s') . PHP_EOL,
        FILE_APPEND
        );
        
        if ($this->helperinventoryupdate->isEnabled()) {
            try {

                $apiusername = $this->helperinventoryupdate->Apiusername();
                $apipassword = $this->helperinventoryupdate->Apipassword();
                $schemalocationapiurl = $this->helperinventoryupdate->Schemalocationapiurl();
                $endpointsapiurl = $this->helperinventoryupdate->Endpointsapiurl();

                $mode = "false";
                if ($this->helperinventoryupdate->Testmodecheck()) {
                    $mode = "true";
                }

                $current_date = gmDate("Y-m-d\TH:i:s\Z");
                $nasc = substr(md5(uniqid($apipassword, true)), 0, 16);
                $nonce = base64_encode($nasc);

                $auth = new \stdClass();
                $auth->Username = $apiusername;
                $auth->Nonce = $nonce;
                $auth->Created = $current_date;
                $auth->Password = base64_encode(sha1($auth->Nonce . $auth->Created . $apipassword));

                $soapClient = new \SoapClient($schemalocationapiurl);

                $headers = new \SoapHeader($endpointsapiurl, 'AuthHeader', $auth);

                // Prepare Soap Client Header
                $soapClient->__setSoapHeaders($headers);

                // Setup body the GetStockFile parameters
                $ap_param['TestMode'] = $mode;
                $ap_param['StockFileFormat'] = "JSON";
                $ap_param['FieldDelimiter'] = ",";
                $ap_param['StockFileFields']['StockFileField'] = "StockLevel";
                $ap_param['SortBy'] = "StockLevel";

                $response = $soapClient->GetStockFile($ap_param);

                $stockdatarray = json_decode($response->File, true);

                $this->_logger->info('**************** Current Date **************** ::');
                $this->_logger->info(gmDate("Y-m-d H:i:s"));

                $api_product_sku_array = array();
                foreach ($stockdatarray as $value) {
                    /* print_r($value['Quantity']);
                      print_r($value['StockLevel']); */
                    $api_product_sku_array [] = $value['StockCode'];
                    $productcollection = $this->_productFactory->create()->addAttributeToFilter('sku', ['eq' => $value['StockCode']]);

                    if ($productcollection->getData()) {
                        $this->_logger->info('****************  StockCode **************** ::');
                        $this->_logger->info($value['StockCode']);

                        $this->_logger->info('**************** StockLevel **************** ::');
                        $this->_logger->info($value['StockLevel']);

                        $productData = $productcollection->getData();
                        $product = $this->_ProductRepository->get($productData[0]['sku']);
                        $stockItem = $product->getExtensionAttributes()->getStockItem();

                        $stockItem->setQty($value['StockLevel']);
                        $stockItem->setIsInStock($value['StockLevel'] > 0 ? 1 : 0);
                        $product->save();
                    }

                }
                if (!empty($api_product_sku_array)) {
                    $get_all_product_collection_not_in_api = $this->_productFactory->create()->addFieldToFilter('sku', array('nin' => $api_product_sku_array));
                    foreach ($get_all_product_collection_not_in_api as $product_data_not_in_api) {
                        $this->_logger->info('**************** Set Product Quantity as a zero.  ****************');
                        $this->_logger->info($product_data_not_in_api->getSku());
                        $product = $this->_ProductRepository->get($product_data_not_in_api->getSku());
                        $stockItem = $product->getExtensionAttributes()->getStockItem();
                        $stockItem->setQty(0);
                        $stockItem->setIsInStock(0);
                        $product->save();
                    }
                    $this->_logger->info('**************** Stop  ****************');
                }
            } catch (SoapFault $fault) {
                $this->_logger->info("***** Error SoapFault *****");
                $this->_logger->info('***** $fault->faultcode *****"');
                $this->_logger->info($fault->faultcode);
                $this->_logger->info('***** $fault->faultstring *****"');
                $this->_logger->info($fault->faultstring);
            } catch (\Exception $e) {
                $this->_logger->info("***** Error  Exception*****");
                $this->_logger->info($e->getMessage());
            }
        }
    }
}