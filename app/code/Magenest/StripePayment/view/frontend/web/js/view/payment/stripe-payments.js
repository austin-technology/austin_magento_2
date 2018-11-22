/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*browser:true*/
/*global define*/
define(
    [
        'jquery',
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        $,
        Component,
        rendererList
    ) {
        'use strict';

        var api = window.checkoutConfig.payment.magenest_stripe.api;
        var stripeMethod;
        if(api === "v2") {
            stripeMethod = {
                type: 'magenest_stripe',
                component: 'Magenest_StripePayment/js/view/payment/method-renderer/stripe-payments-method'
            }
        }
        if(api === "direct") {
            stripeMethod = {
                type: 'magenest_stripe',
                component: 'Magenest_StripePayment/js/view/payment/method-renderer/stripe-payments-direct'
            }
        }
        if(api === "v3") {
            stripeMethod = {
                type: 'magenest_stripe',
                component: 'Magenest_StripePayment/js/view/payment/method-renderer/stripe-payments-element'
            }
        }

        var methods = [
            stripeMethod,
            {
                type: 'magenest_stripe_iframe',
                component: 'Magenest_StripePayment/js/view/payment/method-renderer/stripe-payments-iframe'
            },
            {
                type: 'magenest_stripe_applepay',
                component: 'Magenest_StripePayment/js/view/payment/method-renderer/stripe-payment-applepay'
            },
            {
                type: 'magenest_stripe_giropay',
                component: 'Magenest_StripePayment/js/view/payment/method-renderer/stripe-payments-giropay'
            },
            {
                type: 'magenest_stripe_alipay',
                component: 'Magenest_StripePayment/js/view/payment/method-renderer/stripe-payments-alipay'
            },
            {
                type: 'magenest_stripe_sofort',
                component: 'Magenest_StripePayment/js/view/payment/method-renderer/stripe-payments-sofort'
            },
            {
                type: 'magenest_stripe_ideal',
                component: 'Magenest_StripePayment/js/view/payment/method-renderer/stripe-payments-ideal'
            },
            {
                type: 'magenest_stripe_bancontact',
                component: 'Magenest_StripePayment/js/view/payment/method-renderer/stripe-payments-bancontact'
            },
            {
                type: 'magenest_stripe_p24',
                component: 'Magenest_StripePayment/js/view/payment/method-renderer/stripe-payments-przelewy'
            },
            {
                type: 'magenest_stripe_eps',
                component: 'Magenest_StripePayment/js/view/payment/method-renderer/stripe-payments-eps'
            },
            {
                type: 'magenest_stripe_multibanco',
                component: 'Magenest_StripePayment/js/view/payment/method-renderer/stripe-payments-multibanco'
            }
        ];

        $.each(methods, function (k, method) {
            rendererList.push(method);
        });
        /** Add view logic here if needed */
        return Component.extend({});
    }
);