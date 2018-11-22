<?php
namespace Magento\Sales\Api\Data;

/**
 * Extension class for @see \Magento\Sales\Api\Data\OrderInterface
 */
class OrderExtension extends \Magento\Framework\Api\AbstractSimpleObject implements OrderExtensionInterface
{
    /**
     * @return \Magento\Sales\Api\Data\ShippingAssignmentInterface[]|null
     */
    public function getShippingAssignments()
    {
        return $this->_get('shipping_assignments');
    }

    /**
     * @param \Magento\Sales\Api\Data\ShippingAssignmentInterface[] $shippingAssignments
     * @return $this
     */
    public function setShippingAssignments($shippingAssignments)
    {
        $this->setData('shipping_assignments', $shippingAssignments);
        return $this;
    }

    /**
     * @return \Magento\GiftMessage\Api\Data\MessageInterface|null
     */
    public function getGiftMessage()
    {
        return $this->_get('gift_message');
    }

    /**
     * @param \Magento\GiftMessage\Api\Data\MessageInterface $giftMessage
     * @return $this
     */
    public function setGiftMessage(\Magento\GiftMessage\Api\Data\MessageInterface $giftMessage)
    {
        $this->setData('gift_message', $giftMessage);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getAwOrderNote()
    {
        return $this->_get('aw_order_note');
    }

    /**
     * @param string $awOrderNote
     * @return $this
     */
    public function setAwOrderNote($awOrderNote)
    {
        $this->setData('aw_order_note', $awOrderNote);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getAwDeliveryDate()
    {
        return $this->_get('aw_delivery_date');
    }

    /**
     * @param string $awDeliveryDate
     * @return $this
     */
    public function setAwDeliveryDate($awDeliveryDate)
    {
        $this->setData('aw_delivery_date', $awDeliveryDate);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getAwDeliveryDateFrom()
    {
        return $this->_get('aw_delivery_date_from');
    }

    /**
     * @param string $awDeliveryDateFrom
     * @return $this
     */
    public function setAwDeliveryDateFrom($awDeliveryDateFrom)
    {
        $this->setData('aw_delivery_date_from', $awDeliveryDateFrom);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getAwDeliveryDateTo()
    {
        return $this->_get('aw_delivery_date_to');
    }

    /**
     * @param string $awDeliveryDateTo
     * @return $this
     */
    public function setAwDeliveryDateTo($awDeliveryDateTo)
    {
        $this->setData('aw_delivery_date_to', $awDeliveryDateTo);
        return $this;
    }

    /**
     * @return \Magento\Tax\Api\Data\OrderTaxDetailsAppliedTaxInterface[]|null
     */
    public function getAppliedTaxes()
    {
        return $this->_get('applied_taxes');
    }

    /**
     * @param \Magento\Tax\Api\Data\OrderTaxDetailsAppliedTaxInterface[] $appliedTaxes
     * @return $this
     */
    public function setAppliedTaxes($appliedTaxes)
    {
        $this->setData('applied_taxes', $appliedTaxes);
        return $this;
    }

    /**
     * @return \Magento\Tax\Api\Data\OrderTaxDetailsItemInterface[]|null
     */
    public function getItemAppliedTaxes()
    {
        return $this->_get('item_applied_taxes');
    }

    /**
     * @param \Magento\Tax\Api\Data\OrderTaxDetailsItemInterface[] $itemAppliedTaxes
     * @return $this
     */
    public function setItemAppliedTaxes($itemAppliedTaxes)
    {
        $this->setData('item_applied_taxes', $itemAppliedTaxes);
        return $this;
    }

    /**
     * @return boolean|null
     */
    public function getConvertingFromQuote()
    {
        return $this->_get('converting_from_quote');
    }

    /**
     * @param boolean $convertingFromQuote
     * @return $this
     */
    public function setConvertingFromQuote($convertingFromQuote)
    {
        $this->setData('converting_from_quote', $convertingFromQuote);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getAmazonOrderReferenceId()
    {
        return $this->_get('amazon_order_reference_id');
    }

    /**
     * @param string $amazonOrderReferenceId
     * @return $this
     */
    public function setAmazonOrderReferenceId($amazonOrderReferenceId)
    {
        $this->setData('amazon_order_reference_id', $amazonOrderReferenceId);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getShipStore()
    {
        return $this->_get('ship_store');
    }

    /**
     * @param string $shipStore
     * @return $this
     */
    public function setShipStore($shipStore)
    {
        $this->setData('ship_store', $shipStore);
        return $this;
    }
}
