<?php

namespace ICEShop\ICECatConnect\Api;

interface ICECatConnectInterface
{
    /**
     * Check version of module
     *
     * @return string
     */
    public function getICEshopIcecatconnectorExtensionVersion();

    /**
     * Get products from shop
     * @param  mixed $data
     * @return string
     */
    public function getProductsBatch($data);

    /**
     * Getting languages settings
     * @return string
     */
    public function getLanguageMapping();

    /**
     * Retrieve attribute set list
     * @param  mixed $data
     * @return string
     */
    public function catalogProductAttributeSetList($data);

    /**
     * Fetching attribute lists for attribute set batch
     *
     * @param mixed
     * @return string
     */
    public function getProductAttributeList($data);

    /**
     * Save products data
     * @param  mixed $data
     * @return string
     */
    public function saveAttributeSetBatch($data);

    /**
     * Method to save updated products data as a batch (speeds up import process)
     *
     * @param mixed $data
     * @return string
     */
    public function saveProductsBatch($data);

    /**
     * Add products images queue
     *
     * @param mixed $data
     * @return string
     */
    public function queueProductsImages($data);

    /**
     * Update language codes
     *
     * @param mixed $data
     * @return string
     */

    public function updateLanguageCodes($data);

    /**
     * Set indexer mode
     *
     * @param mixed $data
     * @return string
     */
    public function setIndexerMode($data);

    /**
     * Run full reindex
     *
     * @return string
     */
    public function runFullReindex();

    /**
     * Download images
     * @param mixed $data
     * @return string
     */
    public function processProductsImagesQueue($data);

    /**
     * Try re-upload images
     * @param mixed $data
     * @return string
     */
    public function processBrokenProductsImages($data);

    /**
     * Fix failed attributes
     *
     * @param mixed $data
     * @return string
     */
    public function fixFailedAttributes();

    /**
     * Set image as default
     *
     * @param mixed $data
     * @return string
     */
    public function processDefaultProductsImages($data);

    /**
     * Method to save updated products data as a batch (speeds up import process)
     *
     * @param mixed $data
     * @return string
     */
    public function saveMainAttributesBatch($data);

    /**
     * Attributes refresh
     *
     * @param mixed $data
     * @return string
     */
    public function attributesRefresh($data);
}
