<?php
/**
 * Created by Magenest.
 * Author: Pham Quang Hau
 * Date: 27/05/2016
 * Time: 00:23
 */

namespace Magenest\StripePayment\Model;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

class Status extends AbstractSource
{
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';

    public static function getOptionArray()
    {
        return [
            self::STATUS_ACTIVE => __('Active'),
            self::STATUS_INACTIVE => __('Inactive')
        ];
    }

    public function getAllOptions()
    {
        $result = [];
        foreach (self::getOptionArray() as $index => $value) {
            $result[] = ['value' => $index, 'label' => $value];
        }

        return $result;
    }

    public function getOptionGrid($optionId)
    {
        $options = self::getOptionArray();
        if ($optionId == self::STATUS_ACTIVE) {
            $html = '<span class="grid-severity-notice"><span>' . $options[$optionId] . '</span>' . '</span>';
        } else {
            $html = '<span class="grid-severity-critical"><span>' . $options[$optionId] . '</span></span>';
        }

        return $html;
    }
}
