<?php

namespace ICEShop\ICECatConnect\Model;

use \Magento\Framework\App\ObjectManager;
use Magento\Framework\Config\ConfigOptionsListConstants;

/**
 * Custom class for working with data in database
 */
class ICECatConnectDB
{

    /*
     * Connection
     */
    public $connection = null;

    /*
     * Resource
     */
    public $resource = null;

    /*
     *  Instance of Magento Object Manager
     */
    public $objectManager = null;

    /*
     * Table for DB tables in Magento
     */
    public $tablePrefix = null;

    /*
     *  All conversions that exists at `icecat_imports_conversions_rules` table
     */
    public $conversionsRules = null;

    public function __construct()
    {
        // Init object manager instance
        if (!$this->connection) {
            $this->objectManager = ObjectManager::getInstance();
        }

        // Init instance of connection
        if (!$this->connection) {
            $resource = $this->objectManager->create('\Magento\Framework\App\ResourceConnection');
            $this->connection = $resource->getConnection(
                \Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION
            );
        }
        // Init instance of resource
        if (!$this->resource) {
            $this->resource = $this->objectManager->create('\Magento\Framework\App\ResourceConnection');
        }

        $deployConfig = $this->objectManager->get('Magento\Framework\App\DeploymentConfig');
        $this->tablePrefix = (string)$deployConfig->get(ConfigOptionsListConstants::CONFIG_PATH_DB_PREFIX);
    }


    /**
     * @param $sql
     * @param array $parameters
     * @return mixed
     */
    public function executeStatements($sql, $parameters = [])
    {
        $statement = str_replace('{prefix}', $this->tablePrefix, $sql);

        if (!empty($parameters))
            $query = $this->connection->query($statement, $parameters);
        else
            $query = $this->connection->query($statement);

        $fetch = $query->fetchAll();

        return $fetch;
    }

    public function fetchSingleValue($sql, $parameters = [], $field = false)
    {
        $return = false;

        $statement = str_replace('{prefix}', $this->tablePrefix, $sql);

        if (!empty($parameters))
            $query = $this->connection->query($statement, $parameters);
        else
            $query = $this->connection->query($statement);


        $fetchStatement = $query->fetch();

        if ($field) {
            if (isset($fetchStatement[$field])) {
                $return = $fetchStatement[$field];
            }
        }

        return $return;
    }

    /**
     * Save conversions
     *
     * @param $symbol
     * @param $original_id
     * @param $type
     */
    public function saveConversions($symbol, $original_id, $type)
    {
        $this->connection->query(
            "INSERT INTO icecat_imports_conversions_rules_{$type} (
                imports_conversions_rules_symbol, 
                imports_conversions_rules_original) 
                VALUES 
                (:imports_conversions_rules_symbol, 
                :imports_conversions_rules_original)
                 ON DUPLICATE KEY UPDATE updated=NOW()
                ",
            [
                ':imports_conversions_rules_original' => $original_id,
                ':imports_conversions_rules_symbol' => $symbol
            ]
        );
    }

    public function _saveConversions($symbol, $original_id, $type)
    {
        $this->saveConversions($symbol, $original_id, $type);
    }

    /**
     * @param $type
     * @return mixed
     */
    public function _getConversionsRules($type)
    {
        $results = $this->connection->fetchAll(
            "SELECT `imports_conversions_rules_original`, `imports_conversions_rules_symbol` 
              FROM icecat_imports_conversions_rules_{$type} ORDER BY updated;"
        );
        foreach ($results as $row) {
            $this->conversionsRules[$type][$row['imports_conversions_rules_original']] =
                $row['imports_conversions_rules_symbol'];
        }
        return $this->conversionsRules;
    }

    /**
     * @param $original_id
     * @param $type
     * @return bool
     */
    public function getConversionRule($original_id, $type)
    {

        if (empty($this->conversionsRules[$type])) {
            $this->_getConversionsRules($type);
        }
        if (!empty($this->conversionsRules[$type][$original_id])) {
            return $this->conversionsRules[$type][$original_id];
        }

        return false;
    }
}