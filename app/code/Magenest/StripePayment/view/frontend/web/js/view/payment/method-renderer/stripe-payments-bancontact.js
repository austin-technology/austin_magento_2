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
                template: 'Magenest_StripePayment/payment/stripe-payments-bancontact',
                redirectAfterPlaceOrder: false
            },
            messageContainer: messageContainer,
            languageList: ko.observableArray(JSON.parse(window.checkoutConfig.payment.magenest_stripe_bancontact.language_list)),
            languageValue: ko.observable(""),
            allowSelectLanguage: ko.observable(""),

            initObservable: function(){
                var self = this;
                this._super();
                this.allowSelectLanguage(window.checkoutConfig.payment.magenest_stripe_bancontact.allow_select_language);
                this.languageValue(window.checkoutConfig.payment.magenest_stripe_bancontact.default_language);
                return this;
            },

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
                                url: url.build('stripe/checkout_bancontact/source'),
                                dataType: "json",
                                data: {
                                    form_key: $.cookie('form_key'),
                                    language: self.languageValue()
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
                return window.checkoutConfig.payment.magenest_stripe_config.icon.magenest_stripe_bancontact;
            }
        });
    }
);