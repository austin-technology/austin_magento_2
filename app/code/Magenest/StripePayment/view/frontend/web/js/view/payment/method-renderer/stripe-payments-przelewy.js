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
        'Magento_Checkout/js/action/set-payment-information',
        'Magento_Checkout/js/model/full-screen-loader',
        'Magento_Checkout/js/model/payment/additional-validators',
        'Magento_Ui/js/model/messages',
        'mage/url',
        'mage/cookies'
    ],
    function ($,
              ko,
              Component,
              setPaymentInformationAction,
              fullScreenLoader,
              additionalValidators,
              messageContainer,
              url
    ) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Magenest_StripePayment/payment/stripe-payments-przelewy',
                redirectAfterPlaceOrder: false
            },
            messageContainer: messageContainer,

            placeOrder: function (data, event) {
                if (event) {
                    event.preventDefault();
                }
                var self = this;
                if (this.validate() && additionalValidators.validate()) {
                    fullScreenLoader.startLoader();
                    self.isPlaceOrderActionAllowed(false);
                    $.when(
                        setPaymentInformationAction(this.messageContainer, self.getData())
                    ).done(
                        function () {
                            self.isPlaceOrderActionAllowed(false);
                            fullScreenLoader.startLoader();
                            $.ajax({
                                url: url.build('stripe/checkout_przelewy/source'),
                                dataType: "json",
                                data: {
                                    form_key: $.cookie('form_key')
                                },
                                type: 'POST',
                                success: function (response) {
                                    if (response.success) {
                                        $.mage.redirect(response.redirect_url);
                                    }
                                    if (response.error) {
                                        self.isPlaceOrderActionAllowed(true);
                                        fullScreenLoader.stopLoader();
                                        self.messageContainer.addErrorMessage({
                                            message: response.message
                                        });
                                    }
                                },
                                error: function () {
                                    self.isPlaceOrderActionAllowed(true);
                                    fullScreenLoader.stopLoader();
                                    self.messageContainer.addErrorMessage({
                                        message: 'Something went wrong, please try again.'
                                    });
                                }
                            });
                        }
                    ).always(
                        function () {
                            self.isPlaceOrderActionAllowed(true);
                            fullScreenLoader.stopLoader();
                        }
                    );

                    return true;
                }
                return false;
            },

            getIcons: function () {
                return window.checkoutConfig.payment.magenest_stripe_config.icon.magenest_stripe_p24;
            }
        });
    }
);