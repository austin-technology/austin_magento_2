/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * @api
 */
define([
    '../model/quote',
    'jquery'
], function (quote,$) {
    'use strict';

    return function (shippingMethod) {
		if($('#unchecked').val()==1){	
			$("#uncheckedvalidation").val('1');			 
			quote.shippingMethod(shippingMethod);
		}else{
			setTimeout(function() {
				setTimeout($('#unchecked').val('1'));
			}, 300);
			
			quote.shippingMethod('check');
		}
    };
});
