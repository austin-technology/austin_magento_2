<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Mostviewed
 */


namespace Amasty\Mostviewed\Controller\Adminhtml\Rule;

class Save extends \Amasty\Mostviewed\Controller\Adminhtml\Rule
{
    public function execute()
    {
        if ($this->getRequest()->getPostValue()) {
            /** @var \Amasty\Mostviewed\Model\Rule $model */
            $model = $this->ruleFactory->create();

            try {
                $data = $this->getRequest()->getPostValue();
                $id = $this->getRequest()->getParam('rule_id');
                if ($id) {
                    $model = $this->ruleRepository->get($id);
                }
                $validateResult = $model->validateData($this->dataObject->addData($data));
                if ($validateResult !== true) {
                    foreach ($validateResult as $errorMessage) {
                        $this->messageManager->addErrorMessage($errorMessage);
                    }
                    $this->_getSession()->setPageData($data);
                    $this->dataPersistor->set('amasty_mostviewed_rule', $data);
                    $this->_redirect('amasty_mostviewed/*/edit', ['id' => $model->getRuleId()]);
                    return;
                }
                if (isset($data['rule'])) {
                    if (isset($data['rule']['conditions'])) {
                        $data['conditions'] = $data['rule']['conditions'];
                    }
                    unset($data['rule']);
                }

                $model->loadPost($data);
                $this->_getSession()->setPageData($data);
                $this->dataPersistor->set('amasty_mostviewed_rule', $data);

                $this->ruleRepository->save($model);

                $this->messageManager->addSuccessMessage(__('The rule is saved.'));
                $this->_getSession()->setPageData(false);
                $this->dataPersistor->clear('amasty_mostviewed_rule');

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('amasty_mostviewed/*/edit', ['id' => $model->getRuleId()]);
                    return;
                }
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $id = (int)$this->getRequest()->getParam('id');
                if (!empty($id)) {
                    $this->_redirect('amasty_mostviewed/*/edit', ['id' => $id]);
                } else {
                    $this->_redirect('amasty_mostviewed/*/new');
                }
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    __('Something went wrong while saving the rule data. Please review the error log.')
                );
                $this->logger->critical($e);
                $this->_getSession()->setPageData($data);
                $this->dataPersistor->set('amasty_mostviewed_rule', $data);
                $this->_redirect('amasty_mostviewed/*/edit', ['id' => $this->getRequest()->getParam('rule_id')]);
                return;
            }
        }
        $this->_redirect('amasty_mostviewed/*/');
    }
}
