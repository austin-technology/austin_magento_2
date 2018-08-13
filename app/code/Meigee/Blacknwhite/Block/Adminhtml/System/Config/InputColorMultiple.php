<?php
namespace Meigee\Blacknwhite\Block\Adminhtml\System\Config;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class InputColorMultiple extends Field
{

    public function __construct(
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    protected function _getElementHtml(AbstractElement $element)
    {
        $element->addClass('meigee-color-picker');
		$default_name = $element->getName();
		$element_vals = $element->getValue();
		$element_vals = explode('; ', $element_vals);
		
		$ids = array(null => '_d', 'Hover' => '_h', 'Active' => '_a', 'Focus' => '_f');
		$el_id = $element->getId();
		
		$start = strpos($default_name, '[fields]');
		$length = strlen($default_name);
		$end = strpos($default_name, '[value]');
		$end = $length - $end + 1;
		$val_res = substr($default_name, -$start, -$end);	
		$html = '<div class="input-group meigee-group">';
		$i = 0;
		$label = '';
		foreach($ids as $key => $id) {
			$new_name = str_replace($val_res, $val_res.$id, $default_name);
			$element->setName($new_name);
			if($key != null) {
				$label = '<label>'.$key.'</label>';
			}
			$element->setId($el_id.$id);
			$element->setValue($element_vals[$i]);
			
			$html .= "<div class='input-box'>".$label."<input type='text' id='".$element->getId()."' class='meigee-color-picker multiple' name='".$element->getName()."' value='".$element_vals[$i]."'></div>";
			$i++;
		}
		$html .= "<input type='hidden' name='".$default_name."' class='multiple-result' value=''>";
		$html .= "</div>";
		
        return $html;
    }
}
