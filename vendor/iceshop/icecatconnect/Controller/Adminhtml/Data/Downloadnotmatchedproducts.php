<?php
namespace ICEShop\ICECatConnect\Controller\Adminhtml\Data;

class Downloadnotmatchedproducts extends \Magento\Framework\App\Action\Action
{
    public function execute()
    {
        header('Content-Description: File Transfer');
        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename=not_matched_products.csv');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');

        $headers = ['ID', 'Name', 'MPN', 'Brand', 'GTIN'];
        echo implode("\t", $headers) . PHP_EOL;

        $objectManager      = \Magento\Framework\App\ObjectManager::getInstance();
        $attributesObject   = $objectManager->get('\Magento\Eav\Model\Config');
        $configObject       = $objectManager->get('\Magento\Framework\App\Config\ScopeConfigInterface');
        $dbResource         = $objectManager->get('Magento\Framework\App\ResourceConnection');
        $dbConnection       = $dbResource->getConnection();

        $storeId = $objectManager->create('\Magento\Store\Model\StoreManagerInterface')->getStore()->getId();

        foreach($attributesObject->getAttribute('catalog_product', 'active_ice')->getSource()->getAllOptions() as $option) {
            if($option['label'] == 'Yes') {
                $isActiveValue = $option['value'];
            }
        }

        $nameAttributeId = $attributesObject->getAttribute('catalog_product', 'name')->getData()['attribute_id'];
        $mpnAttributeId = $attributesObject->getAttribute(
            'catalog_product',
            $configObject->getValue('iceshop_icecatconnect/icecatconnect_products_mapping/products_mapping_mpn')
        )->getData()['attribute_id'];
        $brandAttributeId = $attributesObject->getAttribute(
            'catalog_product',
            $configObject->getValue('iceshop_icecatconnect/icecatconnect_products_mapping/products_mapping_brand')
        )->getData()['attribute_id'];
        $gtinAttributeId = $attributesObject->getAttribute(
            'catalog_product',
            $configObject->getValue('iceshop_icecatconnect/icecatconnect_products_mapping/products_mapping_gtin')
        )->getData()['attribute_id'];

        $productsTable = $dbResource->getTableName('catalog_product_entity');
        $varcharAttributeTable = $dbResource->getTableName('catalog_product_entity_varchar');
        $sqlStatement = "
            SELECT 
                product.entity_id   AS id,
                product_name.value  AS name,
                mpn.value           AS mpn,
                brand.value         AS brand,
                gtin.value          AS gtin
            FROM
                $productsTable AS product
            LEFT JOIN
                $varcharAttributeTable AS mpn           ON product.entity_id = mpn.entity_id AND mpn.store_id = :store_id AND mpn.attribute_id = :mpn_attribute_id
            LEFT JOIN
                $varcharAttributeTable AS brand         ON product.entity_id = brand.entity_id AND brand.store_id = :store_id AND brand.attribute_id = :brand_attribute_id
            LEFT JOIN
                $varcharAttributeTable AS gtin          ON product.entity_id = gtin.entity_id AND gtin.store_id = :store_id AND gtin.attribute_id = :gtin_attribute_id
            LEFT JOIN
                $varcharAttributeTable AS product_name  ON product.entity_id = product_name.entity_id AND product_name.store_id = :store_id AND product_name.attribute_id = :name_attribute_id
            WHERE
                product.active_ice = :active AND (product.updated_ice = :date_default OR product.updated_ice IS NULL)
            GROUP BY 
                product.entity_id
        ";
        $bindedValues = [
            ':store_id'             => $storeId,
            ':active'               => $isActiveValue,
            ':date_default'         => '0000-00-00 00:00:00',
            ':mpn_attribute_id'     => $mpnAttributeId,
            ':brand_attribute_id'   => $brandAttributeId,
            ':gtin_attribute_id'    => $gtinAttributeId,
            ':name_attribute_id'    => $nameAttributeId,
        ];

        $products = $dbConnection->fetchAll($sqlStatement, $bindedValues);
        foreach($products as $product) {
            echo implode("\t", array_values($product)) . PHP_EOL;
        }
    }
}