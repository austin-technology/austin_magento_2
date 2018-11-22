<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Mostviewed
 */


namespace Amasty\Mostviewed\Controller\Adminhtml\Rule;

use Magento\Framework\Controller\ResultFactory;

class MassDelete extends \Amasty\Mostviewed\Controller\Adminhtml\Rule
{
    /**
     * Delete action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->ruleCollectionFactory->create());
        $collectionSize = $collection->getSize();

        /** @var \Amasty\Mostviewed\Model\ResourceModel\Rule $rule */
        foreach ($collection as $rule) {
            $rule->delete();
        }

        $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been deleted.', $collectionSize));

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }
}
