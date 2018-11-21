<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Mostviewed
 */


namespace Amasty\Mostviewed\Block\Adminhtml\Rule\Edit\Tab;

use Amasty\Mostviewed\Model\RegistryConstants;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Magento\Backend\Block\Widget\Form\Generic;

class ProductConditions extends Generic implements TabInterface
{
    /**
     * @var \Magento\Backend\Block\Widget\Form\Renderer\Fieldset
     */
    protected $rendererFieldset;

    /**
     * @var \Magento\Rule\Block\Conditions
     */
    protected $conditions;

    /**
     * ProductConditions constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param \Magento\Backend\Block\Widget\Form\Renderer\Fieldset $rendererFieldset
     * @param \Magento\Rule\Block\Conditions $conditions
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        \Magento\Backend\Block\Widget\Form\Renderer\Fieldset $rendererFieldset,
        \Magento\Rule\Block\Conditions $conditions,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->rendererFieldset = $rendererFieldset;
        $this->conditions = $conditions;
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Product Conditions');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Product Conditions');
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        $formName = 'amasty_mostviewed_rule_form';
        /** @var \Amasty\Mostviewed\Model\Rule $model */
        $model = $this->_coreRegistry->registry(RegistryConstants::CURRENT_RULE);
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('rule_');

        /* start condition block*/
        $fieldset = $form->addFieldset(
            'conditions_fieldset',
            ['legend' => __('Conditions')]
        );
        $renderer = $this->rendererFieldset
            ->setTemplate('Amasty_Mostviewed::rule/condition/fieldset.phtml')
            ->setFieldSetId($model->getConditionsFieldSetId($formName))
            ->setNewChildUrl(
                $this->getUrl(
                    'amasty_mostviewed/rule/newConditionHtml/form/' . $model->getConditionsFieldSetId($formName),
                    ['form_namespace' => $formName]
                )
            );

        $fieldset->setRenderer($renderer);

        $fieldset->addField(
            'conditions',
            'text',
            [
                'name' => 'conditions',
                'label' => __('Product Conditions'),
                'title' => __('Product Conditions'),
                'required' => true,
                'data-form-part' => $formName
            ]
        )
            ->setRule($model)
            ->setRenderer($this->conditions);

        $form->setValues($model->getData());
        $this->setConditionFormName($model->getConditions(), $formName, $model->getConditionsFieldSetId($formName));
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * @param \Magento\Rule\Model\Condition\AbstractCondition $conditions
     * @param string $formName
     * @return void
     */
    private function setConditionFormName(
        \Magento\Rule\Model\Condition\AbstractCondition $conditions,
        $formName,
        $fieldsetName
    ) {
        $conditions->setFormName($formName);
        $conditions->setJsFormObject($fieldsetName);
        if ($conditions->getConditions() && is_array($conditions->getConditions())) {
            foreach ($conditions->getConditions() as $condition) {
                $this->setConditionFormName($condition, $formName, $fieldsetName);
            }
        }
    }
}
