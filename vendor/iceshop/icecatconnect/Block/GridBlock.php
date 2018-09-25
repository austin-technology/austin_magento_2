<?php

// @codingStandardsIgnoreFile

namespace ICEShop\ICECatConnect\Block;

/**
 * GridBlock block
 */
class GridBlock extends \Magento\Framework\View\Element\Template
{
    /**
     * @param $data
     * @return string
     */
    public function generateTable($data)
    {
        $return = [];
        $html = '';
        foreach ($data as $key => $value) {
            $html .= '<tr><td>' . $key . ':' . '</td><td>' . $value . '</td></tr>';
        }
        return $html;
    }
}
