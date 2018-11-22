<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Sparx\StorePickup\Api\Data;

/**
 * Order interface.
 *
 * An order is a document that a web store issues to a customer. Magento generates a sales order that lists the product
 * items, billing and shipping addresses, and shipping and payment methods. A corresponding external document, known as
 * a purchase order, is emailed to the customer.
 * @api
 * @since 100.0.2
 */
interface OrderInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{

		const SHIP_STORE = 'ship_store';

		/**
		 * Gets the Ship Store
		 *
		 * @return text
		 */

			public function getShipStore();
		/**
		 * Sets the Ship Store
		 *
		 * @param int $id
		 * @return $this
		 */
			public function setShipStore($id);
			
    }

?>

    
    
    
    
   
