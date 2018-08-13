<?php

namespace Meigee\Blacknwhite\Preference\View;

use Magento\Framework\View\Layout;
use Magento\Framework\View\Layout\Reader\Context;

class Html extends \Magento\Framework\View\Page\Config\Reader\Html
{
    private $_viewCheck;
    public function __construct(\Meigee\Blacknwhite\Model\ViewCheck $viewCheck)
    {
        $this->_viewCheck = $viewCheck;
    }

    public function interpret(Context $readerContext, Layout\Element $currentElement)
    {
        $checkResult = $this->_viewCheck->check($currentElement);
        if ($checkResult === false)
        {
            return $this;
        }
        return parent::interpret($readerContext, $currentElement);
    }
}