<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Mostviewed
 */


namespace Amasty\Mostviewed\Controller\Adminhtml\Rule;

use Amasty\Mostviewed\Model\RegistryConstants;

class Edit extends \Amasty\Mostviewed\Controller\Adminhtml\Rule
{
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            try {
                $model = $this->ruleRepository->get($id);
            } catch (\Magento\Framework\Exception\NoSuchEntityException $exception) {
                $this->messageManager->addErrorMessage(__('This rule no longer exists.'));
                $this->_redirect('amasty_mostviewed/*');
                return;
            }
        } else {
            /** @var \Amasty\Mostviewed\Model\Rule $model */
            $model = $this->ruleFactory->create();
        }

        $model->getConditions()->setFormName('amasty_mostviewed_rule_form');
        $model->getConditions()->setJsFormObject(
            $model->getConditionsFieldSetId($model->getConditions()->getFormName())
        );
        $model->getConditions()->setRuleFactory($this->ruleFactory);

        // set entered data if was error when we do save
        $data = $this->_session->getPageData(true);
        if (!empty($data)) {
            $model->addData($data);
        }

        $this->coreRegistry->register(RegistryConstants::CURRENT_RULE, $model);
        $this->_initAction();

        // set title and breadcrumbs
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Manage Related Product Rule'));
        $this->_view->getPage()->getConfig()->getTitle()->prepend(
            $model->getRuleId() ? $model->getName() : __('New Related Product Rule')
        );

        $breadcrumb = $id ? __('Edit Related Product Rule') : __('New Related Product Rule');
        $this->_addBreadcrumb($breadcrumb, $breadcrumb);

        $this->_view->renderLayout();
    }
}
