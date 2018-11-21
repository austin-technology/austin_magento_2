<?php
/**
 * Created by Magenest.
 * Author: Pham Quang Hau
 * Date: 26/05/2016
 * Time: 22:15
 */

namespace Magenest\StripePayment\Ui\Component\Listing\Columns;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;
use Magenest\StripePayment\Model\Status as StatusModel;

class Status extends Column
{
    protected $urlBuilder;

    protected $_status;

    public function __construct(
        StatusModel $status,
        UrlInterface $urlBuilder,
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components,
        array $data
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->_status = $status;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                if (!empty($item['status'])) {
                    $item['status'] = strip_tags($this->_status->getOptionGrid($item['status']), ENT_IGNORE);
                }
            }
        }

        return $dataSource;
    }
}
