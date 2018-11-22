<?php

// @codingStandardsIgnoreFile

namespace ICEShop\ICECatConnect\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\App\ObjectManager;

class InstallSchema implements InstallSchemaInterface
{
    /*
     * Connection variable
     */
    private $connection = null;

    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $this->_getConnection();

        /**
         * Creating table to contain all conversions rules
         */

        $sql = "CREATE TABLE IF NOT EXISTS `icecat_imports_conversions_rules_attribute` (
  `imports_conversions_rules_id` int(11) NOT NULL AUTO_INCREMENT,
  `imports_conversions_rules_symbol` varchar(255) DEFAULT NULL,
  `imports_conversions_rules_original` smallint(5) unsigned DEFAULT NULL,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE symbol_original (imports_conversions_rules_symbol, imports_conversions_rules_original),
  PRIMARY KEY (`imports_conversions_rules_id`),
  KEY `imports_conversions_rules_original` (`imports_conversions_rules_original`),
  FOREIGN KEY (`imports_conversions_rules_original`) REFERENCES `{$setup->getTable('eav_attribute')}` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

        $this->connection->query($sql);

        $sql = "CREATE TABLE IF NOT EXISTS `icecat_imports_conversions_rules_attribute_set` (
  `imports_conversions_rules_id` int(11) NOT NULL AUTO_INCREMENT,
  `imports_conversions_rules_symbol` varchar(255) DEFAULT NULL,
  `imports_conversions_rules_original` smallint(5) unsigned DEFAULT NULL,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE symbol_original (imports_conversions_rules_symbol, imports_conversions_rules_original),
  PRIMARY KEY (`imports_conversions_rules_id`),
  KEY `imports_conversions_rules_original` (`imports_conversions_rules_original`),
  FOREIGN KEY (`imports_conversions_rules_original`) REFERENCES `{$setup->getTable('eav_attribute_set')}` (`attribute_set_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

        $this->connection->query($sql);

        $sql = "CREATE TABLE IF NOT EXISTS `icecat_imports_conversions_rules_attribute_group` (
  `imports_conversions_rules_id` int(11) NOT NULL AUTO_INCREMENT,
  `imports_conversions_rules_symbol` varchar(255) DEFAULT NULL,
  `imports_conversions_rules_original` smallint(5) unsigned DEFAULT NULL,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,  
  UNIQUE symbol_original (imports_conversions_rules_symbol, imports_conversions_rules_original),
  PRIMARY KEY (`imports_conversions_rules_id`),
  KEY `imports_conversions_rules_original` (`imports_conversions_rules_original`),
  FOREIGN KEY (`imports_conversions_rules_original`) REFERENCES `{$setup->getTable('eav_attribute_group')}` (`attribute_group_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

        $this->connection->query($sql);

        $sql = "CREATE TABLE IF NOT EXISTS `icecat_imports_conversions_rules_attribute_option` (
  `imports_conversions_rules_id` int(11) NOT NULL AUTO_INCREMENT,
  `imports_conversions_rules_symbol` varchar(255) DEFAULT NULL,
  `imports_conversions_rules_original` int(10) unsigned DEFAULT NULL,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE symbol_original (imports_conversions_rules_symbol, imports_conversions_rules_original),
  PRIMARY KEY (`imports_conversions_rules_id`),
  KEY `imports_conversions_rules_original` (`imports_conversions_rules_original`),
  FOREIGN KEY (`imports_conversions_rules_original`) REFERENCES `{$setup->getTable('eav_attribute_option')}` (`option_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

        $this->connection->query($sql);

        $icecat_imports_conversions_exist = $this->connection->fetchAll("SHOW TABLES LIKE 'icecat_imports_conversions';");
        $icecat_imports_conversions_rules_exist = $this->connection->fetchAll("SHOW TABLES LIKE 'icecat_imports_conversions_rules';");

        if (!empty($icecat_imports_conversions_exist) && !empty($icecat_imports_conversions_rules_exist)) {
            $this->connection->beginTransaction();
            $flag = true;
            try {
                $this->migrateConversions();
                $this->connection->commit();
            } catch (\Exception $e) {
                $this->connection->rollback();
                throw new \Exception($e->getMessage());
                $flag = false;
            }
            if ($flag === true) {
                $this->deleteOldserviceTables();
            }
        }

        $sql = "REPLACE INTO `{$setup->getTable('core_config_data')}` (`scope`, `scope_id`, `path`, `value`) VALUES ('default', 0, 'iceshop_default_icecat_languages', 'a:51:{i:0;a:2:{s:5:\"value\";s:2:\"-1\";s:5:\"label\";s:23:\"--Choose the language--\";}i:1;a:2:{s:5:\"value\";s:5:\"ar_AR\";s:5:\"label\";s:6:\"Arabic\";}i:2;a:2:{s:5:\"value\";s:5:\"es_AR\";s:5:\"label\";s:19:\"Argentinian-spanish\";}i:3;a:2:{s:5:\"value\";s:5:\"nl_BE\";s:5:\"label\";s:13:\"Belgian-dutch\";}i:4;a:2:{s:5:\"value\";s:5:\"fr_BE\";s:5:\"label\";s:14:\"Belgian-french\";}i:5;a:2:{s:5:\"value\";s:5:\"de_BE\";s:5:\"label\";s:14:\"Belgian-german\";}i:6;a:2:{s:5:\"value\";s:5:\"pt_BR\";s:5:\"label\";s:20:\"Brazilian-portuguese\";}i:7;a:2:{s:5:\"value\";s:5:\"bg_BG\";s:5:\"label\";s:9:\"Bulgarian\";}i:8;a:2:{s:5:\"value\";s:5:\"ca_ES\";s:5:\"label\";s:7:\"Catalan\";}i:9;a:2:{s:5:\"value\";s:5:\"zh_CN\";s:5:\"label\";s:7:\"Chinese\";}i:10;a:2:{s:5:\"value\";s:5:\"hr_HR\";s:5:\"label\";s:8:\"Croatian\";}i:11;a:2:{s:5:\"value\";s:5:\"cs_CZ\";s:5:\"label\";s:5:\"Czech\";}i:12;a:2:{s:5:\"value\";s:5:\"da_DK\";s:5:\"label\";s:6:\"Danish\";}i:13;a:2:{s:5:\"value\";s:5:\"nl_NL\";s:5:\"label\";s:5:\"Dutch\";}i:14;a:2:{s:5:\"value\";s:5:\"en_US\";s:5:\"label\";s:7:\"English\";}i:15;a:2:{s:5:\"value\";s:5:\"et_ET\";s:5:\"label\";s:8:\"Estonian\";}i:16;a:2:{s:5:\"value\";s:5:\"fi_FI\";s:5:\"label\";s:7:\"Finnish\";}i:17;a:2:{s:5:\"value\";s:5:\"fr_FR\";s:5:\"label\";s:6:\"French\";}i:18;a:2:{s:5:\"value\";s:5:\"ka_GE\";s:5:\"label\";s:8:\"Georgian\";}i:19;a:2:{s:5:\"value\";s:5:\"de_DE\";s:5:\"label\";s:6:\"German\";}i:20;a:2:{s:5:\"value\";s:5:\"el_GR\";s:5:\"label\";s:5:\"Greek\";}i:21;a:2:{s:5:\"value\";s:5:\"he_HE\";s:5:\"label\";s:6:\"Hebrew\";}i:22;a:2:{s:5:\"value\";s:5:\"hu_HU\";s:5:\"label\";s:9:\"Hungarian\";}i:23;a:2:{s:5:\"value\";s:5:\"EN_IN\";s:5:\"label\";s:14:\"Indian-english\";}i:24;a:2:{s:5:\"value\";s:5:\"id_ID\";s:5:\"label\";s:10:\"Indonesian\";}i:25;a:2:{s:5:\"value\";s:5:\"it_IT\";s:5:\"label\";s:7:\"Italian\";}i:26;a:2:{s:5:\"value\";s:5:\"ja_JP\";s:5:\"label\";s:8:\"Japanese\";}i:27;a:2:{s:5:\"value\";s:5:\"ko_KO\";s:5:\"label\";s:6:\"Korean\";}i:28;a:2:{s:5:\"value\";s:5:\"lv_LV\";s:5:\"label\";s:7:\"Latvian\";}i:29;a:2:{s:5:\"value\";s:5:\"lt_LT\";s:5:\"label\";s:10:\"Lithuanian\";}i:30;a:2:{s:5:\"value\";s:5:\"mk_MK\";s:5:\"label\";s:10:\"Macedonian\";}i:31;a:2:{s:5:\"value\";s:5:\"es_MX\";s:5:\"label\";s:15:\"Mexican spanish\";}i:32;a:2:{s:5:\"value\";s:5:\"no_NO\";s:5:\"label\";s:9:\"Norwegian\";}i:33;a:2:{s:5:\"value\";s:5:\"fa_FA\";s:5:\"label\";s:7:\"Persian\";}i:34;a:2:{s:5:\"value\";s:5:\"pl_PL\";s:5:\"label\";s:6:\"Polish\";}i:35;a:2:{s:5:\"value\";s:5:\"pt_PT\";s:5:\"label\";s:10:\"Portuguese\";}i:36;a:2:{s:5:\"value\";s:5:\"ro_RO\";s:5:\"label\";s:8:\"Romanian\";}i:37;a:2:{s:5:\"value\";s:5:\"ru_RU\";s:5:\"label\";s:7:\"Russian\";}i:38;a:2:{s:5:\"value\";s:5:\"sr_RS\";s:5:\"label\";s:7:\"Serbian\";}i:39;a:2:{s:5:\"value\";s:5:\"EN_SG\";s:5:\"label\";s:17:\"Singapore-english\";}i:40;a:2:{s:5:\"value\";s:5:\"sk_SK\";s:5:\"label\";s:6:\"Slovak\";}i:41;a:2:{s:5:\"value\";s:5:\"sl_SL\";s:5:\"label\";s:9:\"Slovenian\";}i:42;a:2:{s:5:\"value\";s:5:\"EN_ZA\";s:5:\"label\";s:20:\"South africa-english\";}i:43;a:2:{s:5:\"value\";s:5:\"es_ES\";s:5:\"label\";s:7:\"Spanish\";}i:44;a:2:{s:5:\"value\";s:5:\"sv_SE\";s:5:\"label\";s:7:\"Swedish\";}i:45;a:2:{s:5:\"value\";s:5:\"DE_CH\";s:5:\"label\";s:12:\"Swiss-german\";}i:46;a:2:{s:5:\"value\";s:5:\"ZH_TW\";s:5:\"label\";s:19:\"Traditional chinese\";}i:47;a:2:{s:5:\"value\";s:5:\"tr_TR\";s:5:\"label\";s:7:\"Turkish\";}i:48;a:2:{s:5:\"value\";s:5:\"uk_UA\";s:5:\"label\";s:9:\"Ukrainian\";}i:49;a:2:{s:5:\"value\";s:5:\"en_EN\";s:5:\"label\";s:10:\"Us english\";}i:50;a:2:{s:5:\"value\";s:5:\"vi_VI\";s:5:\"label\";s:10:\"Vietnamese\";}}');";
        $this->connection->query($sql);

        $sql = "DROP TABLE IF EXISTS `icecatconnector_attribute_connection`;";
        $this->connection->query($sql);

        $sql = "
                CREATE TABLE `icecatconnector_attribute_connection` (
                  `conn_id`              INT(10) UNSIGNED    NOT NULL
                  COMMENT 'Connection ID' AUTO_INCREMENT,
                  `attribute_id_foreign` INT(10) UNSIGNED    NOT NULL,
                  `is_connected`         TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
                  PRIMARY KEY (`conn_id`),
                  UNIQUE KEY `UNQ_ATTRIBUTE_ID_FOREIGN` (`attribute_id_foreign`),
                  KEY `IDX_ICE_ATTRIBUTE_CONNECTION_IS_CONNECTED` (`is_connected`)
                )
                  ENGINE = InnoDB
                  DEFAULT CHARSET = utf8
                  COMMENT = 'Attribute Connection to Any Set';";

        $this->connection->query($sql);

        $sql = "
                CREATE TABLE IF NOT EXISTS `icecat_products_images` (
                  `entity_id`    INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                  `product_id`   INT(10) UNSIGNED NOT NULL
                  COMMENT 'Magento internal ID',
                  `internal_url` VARCHAR(255) NOT NULL DEFAULT '',
                  `external_url` VARCHAR(255) NOT NULL DEFAULT '',
                  `is_default`      INT(1)           NOT NULL DEFAULT 0,
                  `deleted`      INT(1)           NOT NULL DEFAULT 0,
                  `broken`       INT(1)           NOT NULL DEFAULT 0,
                  PRIMARY KEY (`entity_id`),
                  KEY `IDX_PRODUCT_ID` (`product_id`),
                  KEY `IDX_IS_DEFAULT`(`is_default`),
                  KEY `IDX_DELETED`(`deleted`),
                  KEY `IDX_BROKEN`(`broken`),
                  CONSTRAINT `FK_CAT_PRD_ENTT_ENTT_ID_PRODUCT_ID` FOREIGN KEY (`product_id`) 
                  REFERENCES `{$setup->getTable('catalog_product_entity')}` (`entity_id`)
                    ON DELETE CASCADE
                    ON UPDATE CASCADE,
                   UNIQUE KEY `UNQ_PRODUCT_IMAGE` (`product_id`, `external_url`)
                )
                  ENGINE = InnoDB
                  DEFAULT CHARSET utf8;";
        $this->connection->query($sql);

        $sql = "DROP PROCEDURE IF EXISTS FIELD_EXISTS;";
        $this->connection->query($sql);

        $sql = "CREATE PROCEDURE FIELD_EXISTS(
                    OUT _exists    BOOLEAN, -- return value
                    IN  tableName  CHAR(255) CHARACTER SET 'utf8', -- name of table to look for
                    IN  columnName CHAR(255) CHARACTER SET 'utf8', -- name of column to look for
                    IN  dbName     CHAR(255) CHARACTER SET 'utf8'       -- optional specific db
                ) BEGIN
                -- try to lookup db if none provided
                    SET @_dbName := IF(dbName IS NULL, database(), dbName);
                
                    IF CHAR_LENGTH(@_dbName) = 0
                    THEN -- no specific or current db to check against
                        SELECT
                            FALSE
                        INTO _exists;
                    ELSE -- we have a db to work with
                        SELECT
                            IF(count(*) > 0, TRUE, FALSE)
                        INTO _exists
                        FROM information_schema.COLUMNS c
                        WHERE
                            c.TABLE_SCHEMA = @_dbName
                            AND c.TABLE_NAME = tableName
                            AND c.COLUMN_NAME = columnName;
                    END IF;
                END;";
        $this->connection->multiQuery($sql);

        $sql = "CALL FIELD_EXISTS(@_exists, '{$setup->getTable('catalog_product_entity')}', 'updated_ice', NULL);";
        $this->connection->query($sql);

        $sql = "SELECT @_exists;";
        $res = $this->connection->fetchCol($sql);
        if (!array_shift($res)) {
            $sql = "ALTER TABLE `{$setup->getTable('catalog_product_entity')}` 
ADD COLUMN `updated_ice` TIMESTAMP DEFAULT 0 COMMENT 'Iceshop Update Time';";
            $this->connection->query($sql);
        }

        $flag_exists = false;
        $sql = "CALL FIELD_EXISTS(@_exists_2, '{$setup->getTable('catalog_product_entity')}', 'active_ice', NULL);";
        $this->connection->query($sql);

        $eavFactory = ObjectManager::getInstance()->get('Magento\Quote\Setup\QuoteSetup');
        $sql = "SELECT @_exists_2;";
        $res = $this->connection->fetchCol($sql);
        if (!array_shift($res)) {
            $this->connection->query("ALTER TABLE `{$setup->getTable('catalog_product_entity')}`
ADD `active_ice` VARCHAR(255) DEFAULT NULL; ");

            /**
             * Add attributes to the eav/attribute
             */

            $eavFactory->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'active_ice',
                [
                    'label' => 'Update product by IceCat ?',
                    'type' => 'static',
                    'input' => 'select',
                    'required' => false,
                    'option' =>
                        [
                            'values' =>
                                [
                                    0 => 'Yes',
                                    1 => 'No',
                                ],
                        ],
                ]
            );

            $flag_exists = true;
        } else {
            $flag_exists = true;
        }

        $eav = ObjectManager::getInstance()->get('\Magento\Eav\Model\Config');
        $attribute = $eav->getAttribute('catalog_product', 'active_ice')->getData();
        if (isset($attribute['attribute_id'])) {
            $options = $eav->getAttribute('catalog_product', 'active_ice')->getSource()->getAllOptions();
            $optionId = false;
            foreach ($options as $option) {
                if ($option['label'] == 'Yes') {
                    $optionId = $option['value'];
                    break;
                }
            }
            if ($optionId !== false) {
                $this->connection->query("UPDATE {$setup->getTable('eav_attribute')} 
SET `default_value` = '{$optionId}' 
WHERE `attribute_id` = '{$attribute['attribute_id']}';");
                if ($flag_exists) {
                    $this->connection->query(
                        "UPDATE {$setup->getTable('catalog_product_entity')} SET `active_ice` = '{$optionId}';"
                    );
                }
            }
            // Add new Attribute group
            $groupName = 'Iceshop';
            $entityTypeId = $eavFactory->getEntityTypeId('catalog_product');
            $attributeSetId = $eavFactory->getDefaultAttributeSetId($entityTypeId);
            $attributeGroupId = $eavFactory->getAttributeGroupId($entityTypeId, $attributeSetId, $groupName);

            // Add existing attribute to group
            $eavFactory->addAttributeToGroup(
                $entityTypeId,
                $attributeSetId,
                $attributeGroupId,
                $attribute['attribute_id'],
                null
            );
        }

        $setup->endSetup();
    }

    private function _getConnection()
    {
        if (!$this->connection) {
            $resource = ObjectManager::getInstance()->create('\Magento\Framework\App\ResourceConnection');
            $this->connection = $resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
        }
        return $this->connection;
    }

    /**
     * @return array
     */
    public function getConversionsTypesArray()
    {
        try {
            $select = $this->connection->query(
                "SELECT `imports_conversions_type`, `imports_conversions_id` FROM icecat_imports_conversions;"
            );
            $results = $select->fetchAll(\PDO::FETCH_KEY_PAIR);
            if ($results) {
                return $results;
            }
        } catch (\Exception $e) {
            return [];
        }
    }

    public function migrateConversions()
    {
        if (!empty($this->getConversionsTypesArray())) {
            $conversions_types_array = $this->getConversionsTypesArray();
            try {
                foreach ($conversions_types_array as $type => $id) {
                    $this->connection->query("INSERT IGNORE INTO icecat_imports_conversions_rules_{$type} 
                (`imports_conversions_rules_symbol`, `imports_conversions_rules_original`)
                SELECT iicr.`imports_conversions_rules_symbol`, iicr.`imports_conversions_rules_original` 
                FROM icecat_imports_conversions_rules iicr
                WHERE `imports_conversions_id` = $id;");
                }
            } catch (\Exception $e) {
                throw new \Exception($e->getMessage());
            }
        } else {
            throw new \Exception('There is an empty array of conversions types');
        }
    }

    public function deleteOldserviceTables()
    {
        $this->connection->query("DROP TABLE IF EXISTS `icecat_imports_conversions_rules`");
        $this->connection->query("DROP TABLE IF EXISTS `icecat_imports_conversions`");
    }
}
