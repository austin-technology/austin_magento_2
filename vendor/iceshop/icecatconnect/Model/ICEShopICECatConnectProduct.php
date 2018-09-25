<?php

namespace ICEShop\ICECatConnect\Model;

use Magento\Catalog\Model\Product;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ObjectManager;

class ICEShopICECatConnectProduct extends \Magento\Catalog\Model\Product
{
    /*
     * Internal file name of added/uploaded file
     */
    public $currentUploadFileName = false;
    public $mediaGalleryProcessor = null;

    private static function checkVersion()
    {
        $productMetadata = ObjectManager::getInstance()->get('Magento\Framework\App\ProductMetadataInterface');
        $version = $productMetadata->getVersion();
        $checkVersion = '';
        if ((string)substr($version, 0, 3) == '2.0') {
            $checkVersion = '2.0';
        }
        return $checkVersion;
    }

    /**
     * Add image to gallery
     *
     * @param string $file
     * @param null $mediaAttribute
     * @param bool $move
     * @param bool $exclude
     * @param bool $returnFileName
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function addImageToMediaGallery(
        $file,
        $mediaAttribute = null,
        $move = false,
        $exclude = true,
        $returnFileName = false
    ) {
        $this->currentUploadFileName = false;
        if (self::checkVersion() == '2.0') {
            if ($this->getGalleryAttributeBackend()) {
                $fileName = $this->getMediaGalleryProcessor()->addImage(
                    $this,
                    $file,
                    $mediaAttribute,
                    $move,
                    $exclude
                );
            }
            if ($returnFileName) {
                $this->currentUploadFileName = $fileName;
            }
        } else {
            if ($this->hasGalleryAttribute()) {
                $fileName = $this->getMediaGalleryProcessor()->addImage(
                    $this,
                    $file,
                    $mediaAttribute,
                    $move,
                    $exclude
                );
            }
            if ($returnFileName) {
                $this->currentUploadFileName = $fileName;
            }
        }
        return $this;
    }
    /**
     * @return Product\Gallery\Processor
     */
    private function getMediaGalleryProcessor()
    {
        if (self::checkVersion() != '') {
            if (null === $this->mediaGalleryProcessor) {
                $this->mediaGalleryProcessor = \Magento\Framework\App\ObjectManager::getInstance()
                    ->get('Magento\Catalog\Model\Product\Attribute\Backend\AbstractMedia');
            }
            $this->mediaGalleryProcessor->setAttribute($this);
        } else {
            if (null === $this->mediaGalleryProcessor) {
                $this->mediaGalleryProcessor = \Magento\Framework\App\ObjectManager::getInstance()
                    ->get('Magento\Catalog\Model\Product\Gallery\Processor');
            }
        }
        return $this->mediaGalleryProcessor;
    }
}
