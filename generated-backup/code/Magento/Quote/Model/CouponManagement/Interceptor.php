<?php
namespace Magento\Quote\Model\CouponManagement;

/**
 * Interceptor class for @see \Magento\Quote\Model\CouponManagement
 */
class Interceptor extends \Magento\Quote\Model\CouponManagement implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Quote\Api\CartRepositoryInterface $quoteRepository)
    {
        $this->___init();
        parent::__construct($quoteRepository);
    }

    /**
     * {@inheritdoc}
     */
    public function get($cartId)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'get');
        if (!$pluginInfo) {
            return parent::get($cartId);
        } else {
            return $this->___callPlugins('get', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function set($cartId, $couponCode)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'set');
        if (!$pluginInfo) {
            return parent::set($cartId, $couponCode);
        } else {
            return $this->___callPlugins('set', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function remove($cartId)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'remove');
        if (!$pluginInfo) {
            return parent::remove($cartId);
        } else {
            return $this->___callPlugins('remove', func_get_args(), $pluginInfo);
        }
    }
}
