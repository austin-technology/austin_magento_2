<?php
namespace Magento\Framework\View\Page\Config\Structure;

/**
 * Interceptor class for @see \Magento\Framework\View\Page\Config\Structure
 */
class Interceptor extends \Magento\Framework\View\Page\Config\Structure implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct()
    {
        $this->___init();
    }

    /**
     * {@inheritdoc}
     */
    public function setElementAttribute($element, $attributeName, $attributeValue)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setElementAttribute');
        if (!$pluginInfo) {
            return parent::setElementAttribute($element, $attributeName, $attributeValue);
        } else {
            return $this->___callPlugins('setElementAttribute', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function processRemoveElementAttributes()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'processRemoveElementAttributes');
        if (!$pluginInfo) {
            return parent::processRemoveElementAttributes();
        } else {
            return $this->___callPlugins('processRemoveElementAttributes', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setBodyClass($value)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setBodyClass');
        if (!$pluginInfo) {
            return parent::setBodyClass($value);
        } else {
            return $this->___callPlugins('setBodyClass', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getBodyClasses()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getBodyClasses');
        if (!$pluginInfo) {
            return parent::getBodyClasses();
        } else {
            return $this->___callPlugins('getBodyClasses', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getElementAttributes()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getElementAttributes');
        if (!$pluginInfo) {
            return parent::getElementAttributes();
        } else {
            return $this->___callPlugins('getElementAttributes', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setTitle($title)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setTitle');
        if (!$pluginInfo) {
            return parent::setTitle($title);
        } else {
            return $this->___callPlugins('setTitle', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getTitle');
        if (!$pluginInfo) {
            return parent::getTitle();
        } else {
            return $this->___callPlugins('getTitle', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setMetadata($name, $content)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setMetadata');
        if (!$pluginInfo) {
            return parent::setMetadata($name, $content);
        } else {
            return $this->___callPlugins('setMetadata', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadata()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getMetadata');
        if (!$pluginInfo) {
            return parent::getMetadata();
        } else {
            return $this->___callPlugins('getMetadata', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addAssets($name, $attributes)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'addAssets');
        if (!$pluginInfo) {
            return parent::addAssets($name, $attributes);
        } else {
            return $this->___callPlugins('addAssets', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeAssets($name)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'removeAssets');
        if (!$pluginInfo) {
            return parent::removeAssets($name);
        } else {
            return $this->___callPlugins('removeAssets', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function processRemoveAssets()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'processRemoveAssets');
        if (!$pluginInfo) {
            return parent::processRemoveAssets();
        } else {
            return $this->___callPlugins('processRemoveAssets', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getAssets()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getAssets');
        if (!$pluginInfo) {
            return parent::getAssets();
        } else {
            return $this->___callPlugins('getAssets', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function __toArray()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, '__toArray');
        if (!$pluginInfo) {
            return parent::__toArray();
        } else {
            return $this->___callPlugins('__toArray', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function populateWithArray(array $data)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'populateWithArray');
        if (!$pluginInfo) {
            return parent::populateWithArray($data);
        } else {
            return $this->___callPlugins('populateWithArray', func_get_args(), $pluginInfo);
        }
    }
}
