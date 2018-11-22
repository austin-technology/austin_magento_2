<?php
namespace Magento\Sales\Api\Data;

/**
 * ExtensionInterface class for @see \Magento\Sales\Api\Data\OrderInterface
 */
interface OrderExtensionInterface extends \Magento\Framework\Api\ExtensionAttributesInterface
{
    /**
     * @return \Magento\Sales\Api\Data\ShippingAssignmentInterface[]|null
     */
    public function getShippingAssignments();

    /**
     * @param \Magento\Sales\Api\Data\ShippingAssignmentInterface[] $shippingAssignments
     * @return $this
     */
    public function setShippingAssignments($shippingAssignments);

    /**
     * @return \Magento\GiftMessage\Api\Data\MessageInterface|null
     */
    public function getGiftMessage();

    /**
     * @param \Magento\GiftMessage\Api\Data\MessageInterface $giftMessage
     * @return $this
     */
    public function setGiftMessage(\Magento\GiftMessage\Api\Data\MessageInterface $giftMessage);

    /**
     * @return string|null
     */
    public function getAwOrderNote();

    /**
     * @param string $awOrderNote
     * @return $this
     */
    public function setAwOrderNote($awOrderNote);

    /**
     * @return string|null
     */
    public function getAwDeliveryDate();

    /**
     * @param string $awDeliveryDate
     * @return $this
     */
    public function setAwDeliveryDate($awDeliveryDate);

    /**
     * @return string|null
     */
    public function getAwDeliveryDateFrom();

    /**
     * @param string $awDeliveryDateFrom
     * @return $this
     */
    public function setAwDeliveryDateFrom($awDeliveryDateFrom);

    /**
     * @return string|null
     */
    public function getAwDeliveryDateTo();

    /**
     * @param string $awDeliveryDateTo
     * @return $this
     */
    public function setAwDeliveryDateTo($awDeliveryDateTo);

    /**
     * @return \Magento\Tax\Api\Data\OrderTaxDetailsAppliedTaxInterface[]|null
     */
    public function getAppliedTaxes();

    /**
     * @param \Magento\Tax\Api\Data\OrderTaxDetailsAppliedTaxInterface[] $appliedTaxes
     * @return $this
     */
    public function setAppliedTaxes($appliedTaxes);

    /**
     * @return \Magento\Tax\Api\Data\OrderTaxDetailsItemInterface[]|null
     */
    public function getItemAppliedTaxes();

    /**
     * @param \Magento\Tax\Api\Data\OrderTaxDetailsItemInterface[] $itemAppliedTaxes
     * @return $this
     */
    public function setItemAppliedTaxes($itemAppliedTaxes);

    /**
     * @return boolean|null
     */
    public function getConvertingFromQuote();

    /**
     * @param boolean $convertingFromQuote
     * @return $this
     */
    public function setConvertingFromQuote($convertingFromQuote);

    /**
     * @return string|null
     */
    public function getAmazonOrderReferenceId();

    /**
     * @param string $amazonOrderReferenceId
     * @return $this
     */
    public function setAmazonOrderReferenceId($amazonOrderReferenceId);

    /**
     * @return string|null
     */
    public function getShipStore();

    /**
     * @param string $shipStore
     * @return $this
     */
    public function setShipStore($shipStore);
}
