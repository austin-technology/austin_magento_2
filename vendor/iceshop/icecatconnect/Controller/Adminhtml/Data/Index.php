<?php

namespace ICEShop\ICECatConnect\Controller\Adminhtml\Data;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\App\ProductMetadata;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Config\ConfigOptionsListConstants;
use Magento\Framework\Indexer\StateInterface;
use \Magento\Framework\App\ObjectManager;

class Index extends \Magento\Framework\App\Action\Action
{

    private $connection;

    private $resource;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    public function getConnection()
    {
        if (!$this->connection) {
            $resource = ObjectManager::getInstance()->create('\Magento\Framework\App\ResourceConnection');
            $this->connection = $resource->getConnection(
                \Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION
            );
        }
        return $this->connection;
    }

    private function _getResource()
    {
        if (!$this->resource) {
            $this->resource = ObjectManager::getInstance()->create('\Magento\Framework\App\ResourceConnection');
        }
        return $this->resource;
    }

    public function execute()
    {

        $this->_getResource();
        $this->getConnection();

        $block = [];

        $resultPage = $this->resultPageFactory->create();
        $block['icecatconnect_information'] = $resultPage->getLayout()
            ->createBlock('ICEShop\ICECatConnect\Block\GridBlock')
            ->generateTable($this->_getProductsStatistics());

        $block['attributes_content_statistic'] = $resultPage->getLayout()
            ->createBlock('ICEShop\ICECatConnect\Block\GridBlock')
            ->generateTable($this->_getAttributeStatistics());

        $block['image_statistic'] = $resultPage->getLayout()
            ->createBlock('ICEShop\ICECatConnect\Block\GridBlock')
            ->generateTable($this->_getImagesStatistics());

        $jsonData = json_encode($block);
        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody($jsonData);
    }

    private function _getProductsStatistics()
    {
        $scopeConfig = ObjectManager::getInstance()->get('\Magento\Framework\App\Config\ScopeConfigInterface');
        $return = [];
        $allProductsQuery = $this->connection->query("SELECT count(*) as allProducts FROM {$this->resource->
        getTableName('catalog_product_entity')}");
        $allProductsResult = [];
        while ($row = $allProductsQuery->fetch()) {
            $allProductsResult[] = $row;
        }
        $allProducts = (isset($allProductsResult[0]['allProducts'])) ? $allProductsResult[0]['allProducts'] : 0;
        $mappedProductsQuery = $this->connection->query("SELECT count(*) as mappedProducts FROM {$this->resource
        ->getTableName('catalog_product_entity')} WHERE updated_ice <> '0000-00-00 00:00:00'");
        $mappedProductsResult = [];
        while ($row = $mappedProductsQuery->fetch()) {
            $mappedProductsResult[] = $row;
        }
        $mappedProducts = (isset($mappedProductsResult[0]['mappedProducts'])) ?
            $mappedProductsResult[0]['mappedProducts'] : 0;
        $percentMapped = 0;
        if ((!empty($allProducts)) && (!empty($mappedProducts))) {
            $percentMapped = round((($mappedProducts / $allProducts) * 100), 2);
        }
        $lastStarted = $scopeConfig->getValue('icecatconnect_content_last_start');
        $lastFinished = $scopeConfig->getValue('icecatconnect_content_last_finish');
        $return['Total Products Amount'] = $allProducts;
        $return['Mapped Products Amount'] = $mappedProducts . ' (' . $percentMapped . '%)';
        $return['Import last started'] = (!empty($lastStarted)) ? date('Y-m-d H:i:s', $lastStarted) : __('Not started');
        $return['Import last finished'] =
            (!empty($lastFinished)) ? date('Y-m-d H:i:s', $lastFinished) : __('Not finished');
        return $return;
    }

    /**
     * Get attribute statistic
     * @return array
     */
    private function _getAttributeStatistics()
    {
        $return = [];
        $query = "SELECT 'attribute' as idr , count(*) as cnt FROM icecat_imports_conversions_rules_attribute as a1 UNION
SELECT 'attribute_set' as idr , count(*) as cnt FROM icecat_imports_conversions_rules_attribute_set as a2 UNION
SELECT 'attribute_group' as idr , count(*) as cnt FROM icecat_imports_conversions_rules_attribute_group as a3 UNION
SELECT 'attribute_option' as idr , count(*) as cnt FROM icecat_imports_conversions_rules_attribute_option as a4
;";

        $getDataQuery = $this->connection->query($query);
        $getData = [];
        while ($row = $getDataQuery->fetch()) {
            $getData[] = $row;
        }

        $return['Attributes Created'] = 0;
        $return['Attribute Sets Created'] = 0;
        $return['Attribute Groups Created'] = 0;
        $return['Attribute Options Created'] = 0;
        if (!empty($getData)) {
            foreach ($getData as $data) {
                if (isset($data['idr']) && isset($data['cnt'])) {
                    if ($data['idr'] == 'attribute') {
                        $return['Attributes Created'] = $data['cnt'];
                    }
                    if ($data['idr'] == 'attribute_set') {
                        $return['Attribute Sets Created'] = $data['cnt'];
                    }
                    if ($data['idr'] == 'attribute_group') {
                        $return['Attribute Groups Created'] = $data['cnt'];
                    }
                    if ($data['idr'] == 'attribute_option') {
                        $return['Attribute Options Created'] = $data['cnt'];
                    }
                }
            }
        }
        return $return;
    }

    /**
     * Get image statistic on Icecatconnect Information
     * @return array
     */
    private function _getImagesStatistics()
    {
        $return = [];

        $allImagesResult = [];
        $allImagesQuery = $this->connection->query("SELECT count(*) as cnt FROM icecat_products_images");
        while ($row = $allImagesQuery->fetch()) {
            $allImagesResult[] = $row;
        }
        $allImages = (isset($allImagesResult[0]['cnt'])) ? $allImagesResult[0]['cnt'] : 0;

        $downloadedResult = [];
        $downloadedQuery = $this->connection->query("SELECT count(*) as cnt 
FROM icecat_products_images WHERE internal_url <> ''");
        while ($row = $downloadedQuery->fetch()) {
            $downloadedResult[] = $row;
        }
        $downloaded = (isset($downloadedResult[0]['cnt'])) ? $downloadedResult[0]['cnt'] : 0;

        $brokenResult = [];
        $brokenQuery = $this->connection->query("SELECT count(*) as cnt FROM icecat_products_images 
WHERE internal_url = '' AND broken = 1");
        while ($row = $brokenQuery->fetch()) {
            $brokenResult[] = $row;
        }
        $broken = (isset($brokenResult[0]['cnt'])) ? $brokenResult[0]['cnt'] : 0;

        $deletedQuery = $this->connection->fetchAll("SELECT count(*) as cnt FROM icecat_products_images WHERE deleted = 1");
        $deleted = (isset($deletedQuery[0]['cnt'])) ? $deletedQuery[0]['cnt'] : 0;

        $return['Total Images Entries'] = $allImages - $deleted;
        $return['Total Images Downloaded'] = $downloaded;
        $return['Images Waiting Download'] = abs($allImages - $downloaded - $deleted);

        $brokenPercent = 0;
        if ($broken != 0) {
            $brokenPercent = round((($broken / $allImages) * 100), 2);
            $return['Images Waiting Download'] .= ' incl. ' . $broken . ' (' . $brokenPercent . '%) broken images';
        }

        return $return;
    }
}
