/**
 * Created by joel on 31/12/2016.
 */
/*browser:true*/
/*global define*/
define(
    [
        'jquery',
        'ko',
        'Magento_Checkout/js/view/payment/default',
        'Magento_Checkout/js/action/place-order',
        'Magento_Checkout/js/action/set-payment-information',
        'Magento_Checkout/js/model/full-screen-loader',
        'Magento_Checkout/js/checkout-data',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/full-screen-loader',
        'Magento_Checkout/js/action/redirect-on-success',
        'Magento_Checkout/js/model/payment/additional-validators',
        'Magento_Ui/js/model/messages',
        'https://js.stripe.com/v3/'
    ],
    function ($,
              ko,
              Component,
              placeOrderAction,
              setPaymentInformationAction,
              fullScreenLoadern,
              checkoutData,
              quote,
              fullScreenLoader,
              redirectOnSuccessAction,
              additionalValidators,
              messageContainer
    ) {
        'use strict';

        var stripe,
            paymentRequest;
        var totals = quote.totals(),
            zeroDecimal = window.checkoutConfig.payment.magenest_stripe_config.isZeroDecimal,
            currency = totals.base_currency_code;

        return Component.extend({
            defaults: {
                template: 'Magenest_StripePayment/payment/stripe-payments-applepay',
                replacePlaceOrder: Boolean(window.checkoutConfig.payment.magenest_stripe_applepay.replace_placeorder === "1"),
                rawCardData:""
            },
            messageContainer: messageContainer,

            placeOrder: function () {
                var self = this;
                if(additionalValidators.validate()){
                    var amount = totals.base_grand_total;
                    if(zeroDecimal === '0'){
                        amount*=100;
                    }
                    paymentRequest.update({
                        currency: currency.toLowerCase(),
                        total: {
                            label: 'Total',
                            amount: Math.round(amount),
                            pending: true
                        }
                    });
                    paymentRequest.canMakePayment().then(function(result) {
                        console.log(result);
                        if (result) {
                            paymentRequest.show();
                        }else{
                            self.messageContainer.addErrorMessage({
                                message: "Error: Current payment method is unavailable"
                            });
                        }
                    });
                }
            },

            requestPayment: function (data, event, parent) {
                stripe = Stripe(window.checkoutConfig.payment.magenest_stripe_config.publishableKey);
                var self;
                if(typeof parent !== 'undefined'){
                    self = parent;
                }else{
                    self = this;
                }
                var amount = totals.base_grand_total;
                if(zeroDecimal === '0'){
                    amount*=100;
                }
                paymentRequest = stripe.paymentRequest({
                    country: 'US',
                    currency: currency.toLowerCase(),
                    total: {
                        label: 'Total',
                        amount: Math.round(amount),
                        pending: true
                    }
                });

                var elements = stripe.elements();
                if (self.replacePlaceOrder) {
                    var prButton = elements.create('paymentRequestButton', {
                        paymentRequest: paymentRequest,
                        style: {
                            paymentRequestButton: {
                                type: window.checkoutConfig.payment.magenest_stripe_applepay.button_type,
                                theme: window.checkoutConfig.payment.magenest_stripe_applepay.button_theme,
                                height: '40px'
                            }
                        }
                    });
                    // Check the availability of the Payment Request API first.
                    paymentRequest.canMakePayment().then(function (result) {
                        console.log(result);
                        if (result) {
                            prButton.mount('#payment_section');
                        } else {
                            self.messageContainer.addErrorMessage({
                                message: "Error: Current payment method is unavailable"
                            });
                            document.getElementById('payment_section').style.display = 'none';
                        }
                    });
                }

                paymentRequest.on('token', function(ev) {
                    // Send the token to your server to charge it!
                    self.rawCardData = ev.token;
                    self.getPlaceOrderDeferredObject()
                        .fail(function () {
                            ev.complete('fail');
                        })
                        .done(function () {
                                ev.complete('success');
                                self.afterPlaceOrder();
                                if (self.redirectAfterPlaceOrder) {
                                    redirectOnSuccessAction.execute();
                                }
                            }
                        );
                });
            },

            /**
             * @return {*}
             */
            getPlaceOrderDeferredObject: function () {
                return $.when(
                    placeOrderAction(this.getData(), this.messageContainer)
                );
            },

            /**
             * Get payment method data
             */
            getData: function () {
                var self = this;
                return {
                    'method': this.item.method,
                    'additional_data': {
                        "stripe_response": JSON.stringify(self.rawCardData)
                    }
                };
            }

        });

    }
);