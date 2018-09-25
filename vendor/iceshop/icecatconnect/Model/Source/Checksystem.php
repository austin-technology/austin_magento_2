<?php
namespace ICEShop\ICECatConnect\Model\Source;

class Checksystem implements \Magento\Framework\Option\ArrayInterface
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
        return [
            [
                'value' => $this->urlBuilder->getUrl("iceshop_icecatconnect/data/index"),
                'label' => __('')
            ],
        ];
    }
}
