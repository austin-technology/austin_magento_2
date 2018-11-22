<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Mostviewed
 */


namespace Amasty\Mostviewed\Controller\Adminhtml\Rule;

class NewAction extends \Amasty\Mostviewed\Controller\Adminhtml\Rule
{
    public function execute()
    {
        $this->_forward('edit');
    }
}
