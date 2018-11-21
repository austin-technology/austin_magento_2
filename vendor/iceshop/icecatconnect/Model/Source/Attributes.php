<?php
namespace ICEShop\ICECatConnect\Model\Source;

use Magento\Framework\App\ObjectManager;

class Attributes implements \Magento\Framework\Option\ArrayInterface
{

    public $urlBuilder;

    public function __construct(
        \Magento\Backend\Model\UrlInterface $urlBuilder
    ) {
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $return = [
            '' => "--- " . __('Choose attribute') . " ---"
        ];

        /** @var  $coll \Magento\Eav\Model\ResourceModel\Entity\Attribute\Collection */
        $coll = ObjectManager::getInstance()
            ->create(\Magento\Eav\Model\ResourceModel\Entity\Attribute\Collection::class);
        // add filter by entity type to get product attributes only
        // '4' is the default type ID for 'catalog_product' entity - see 'eav_entity_type' table)
        // or skip the next line to get all attributes for all types of entities
        $coll->addFieldToFilter(\Magento\Eav\Model\Entity\Attribute\Set::KEY_ENTITY_TYPE_ID, 4);
        $attrAll = $coll->load()->getItems();

        if (!empty($attrAll)) {
            foreach ($attrAll as $key => $value) {
                $return[$value->getData('attribute_code')] = $value->getData('attribute_code');
            }
        }

        asort($return);

        return $return;
    }
}
