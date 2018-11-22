<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Mostviewed
 */


namespace Amasty\Mostviewed\Controller\Adminhtml\Rule;

class Index extends \Amasty\Mostviewed\Controller\Adminhtml\Rule
{
    public function execute()
    {
        $this->_initAction();
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Related Product Rules'));
        $this->_view->renderLayout();
    }
}
