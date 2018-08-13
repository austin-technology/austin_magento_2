<?php
namespace Meigee\Blacknwhite\Block\Adminhtml\System\Config\CheckboxSwitch;

class OnOff extends \Meigee\Blacknwhite\Block\Adminhtml\System\Config\CheckboxSwitch
{
    function getOnLabel()
    {
        return __('On');
    }
    function getOffLabel()
    {
        return __('Off');
    }
}