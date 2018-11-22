/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*browser:true*/
/*global define*/
define(
    [
        'Magento_Payment/js/view/payment/cc-form',
        'jquery',
        'Magento_Payment/js/model/credit-card-validation/validator'
    ],
    function (Component, $) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Magenest_StripePayment/payment/stripe-payments-direct'
            },

            getCode: function() {
                return 'magenest_stripe';
            },

            validateForm: function (form) {
                return $(form).validation() && $(form).validation('isValid');
            },

            validate: function () {
                return this.validateForm($('#'+this.getCode()+'-form'));
            },

            isActive: function() {
                return true;
            },

            getInstructions: function () {
                return window.checkoutConfig.payment.magenest_stripe.instructions;
            }
        });
    }
);
