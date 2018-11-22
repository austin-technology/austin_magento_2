<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Mostviewed
 */


namespace Amasty\Mostviewed\Controller\Adminhtml\Rule;

class Delete extends \Amasty\Mostviewed\Controller\Adminhtml\Rule
{
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            try {
                $this->ruleRepository->deleteById($id);
                $this->messageManager->addSuccessMessage(__('The rule is deleted.'));
                $this->_redirect('amasty_mostviewed/*/');

                return;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    __('Can\'t delete item right now. Please review the log and try again.')
                );
                $this->logger->critical($e);
                $this->_redirect('amasty_mostviewed/*/edit', ['id' => $id]);

                return;
            }
        }
        $this->_redirect('amasty_mostviewed/*/');
    }
}
