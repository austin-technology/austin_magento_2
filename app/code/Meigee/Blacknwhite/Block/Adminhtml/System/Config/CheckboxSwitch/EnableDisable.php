<?php
namespace Meigee\Blacknwhite\Block\Adminhtml\System\Config\CheckboxSwitch;

class EnableDisable extends \Meigee\Blacknwhite\Block\Adminhtml\System\Config\CheckboxSwitch
{
    function getOnLabel()
    {
        return __('Enable');
    }
    function getOffLabel()
    {
        return __('Disable');
    }
}