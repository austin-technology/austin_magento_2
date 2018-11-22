<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Mostviewed
 */


namespace Amasty\Mostviewed\Model\Rule;

use Amasty\Mostviewed\Model\Rule;
use Magento\Framework\App\Request\DataPersistorInterface;
use Amasty\Mostviewed\Model\ResourceModel\Rule\CollectionFactory;

/**
 * Class DataProvider
 * @package Amasty\Mostviewed\Model\Rule
 */
class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    private $loadedData;

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $urlBuilder;

    /**
     * DataProvider constructor.
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param DataPersistorInterface $dataPersistor
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        DataPersistorInterface $dataPersistor,
        \Magento\Framework\UrlInterface $urlBuilder,
        CollectionFactory $collectionFactory,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        $this->urlBuilder = $urlBuilder;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }

        $items = $this->collection->getItems();
        /** @var Rule $rule */
        foreach ($items as $rule) {
            $this->loadedData[$rule->getRuleId()] = $rule->getData();
        }

        $data = $this->dataPersistor->get('amasty_mostviewed_rule');
        if (!empty($data)) {
            /** @var Rule $rule */
            $rule = $this->collection->getNewEmptyItem();
            $rule->setData($data);
            $this->loadedData[$rule->getId()] = $rule->getData();
            $this->dataPersistor->clear('amasty_mostviewed_rule');
        }

        return $this->loadedData;
    }
}
