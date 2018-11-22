<?php
/**
* MageNative
*
* NOTICE OF LICENSE
*
* Copyright © 2016 MageNative.
* PHP VERSION 5.6.
*
* This source file is subject to the End User License Agreement (EULA)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://magenative.com/license-agreement.txt
*
* @category MageNative
* @package  MageNative_Mobiconnect
* @author   MageNative Core Team <connect@magenative.com>
* @license  http://magenative.com/license-agreement.txt MageNative LICENSE
* @link     http://magenative.com/license-agreement.txt
*
*/

namespace Sparx\StorePickup\Api;

interface OrderManagmentInterface
{ 
	/**
     * Get getShipStore.
     *
     * @param string $id id
     
     *
     * @return \Sparx\StorePickup\Api\Data\OrderInterface
     */
     
	public function getShipStore($id);
   
   
     
}
