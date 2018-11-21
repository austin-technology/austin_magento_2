<?php
/**
 * Created by PhpStorm.
 * User: joel
 * Date: 03/01/2017
 * Time: 00:13
 */

namespace Magenest\StripePayment\Model\Source;

class Image extends \Magento\Config\Model\Config\Backend\Image
{
    const UPLOAD_DIR = 'stripe';

    protected function _getUploadDir()
    {
        return $this->_mediaDirectory->getAbsolutePath($this->_appendScopeInfo(self::UPLOAD_DIR));
    }

    protected function _addWhetherScopeInfo()
    {
        return true;
    }

    protected function _getAllowedExtensions()
    {
        return ['jpg', 'jpeg', 'gif', 'png', 'svg'];
    }
}
