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
        'ko',
        'Magento_Checkout/js/model/quote',
        'Magento_Customer/js/model/customer',
        'Magento_Checkout/js/model/full-screen-loader',
        'Magento_Checkout/js/action/redirect-on-success',
        'Magento_Ui/js/model/messages',
        'Magento_Checkout/js/action/set-payment-information',
        'Magento_Checkout/js/action/set-billing-address',
        'Magento_Checkout/js/model/payment/additional-validators',
        'mage/url',
        'mage/cookies'
    ],
    function (Component,
              $,
              ko,
              quote,
              customer,
              fullScreenLoader,
              redirectOnSuccessAction,
              messageContainer,
              setPaymentInformationAction,
              setBillingAddressAction,
              additionalValidators,
              url
    ) {
        'use strict';

        var stripe, elements, card;

        return Component.extend({
            defaults: {
                template: 'Magenest_StripePayment/payment/stripe-payments-element',
                redirectAfterPlaceOrder: false,
                saveCardConfig: window.checkoutConfig.payment.magenest_stripe.isSave,
                isLogged: window.checkoutConfig.payment.magenest_stripe_config.isLogin,
                customerCard: ko.observableArray(JSON.parse(window.checkoutConfig.payment.magenest_stripe.saveCards)),
                cardId: ko.observable(0),
                isSelectCard: ko.observable(false),
                hasCard: window.checkoutConfig.payment.magenest_stripe.hasCard,
                saveCardOption: "",
                source: "",
                showPaymentField: ko.observable(false)
            },
            messageContainer: messageContainer,

            initObservable: function () {
                var self = this;
                this._super();
                this.isSelectCard = ko.computed(function () {
                    if ((typeof self.cardId() !== 'undefined')&&(self.hasCard)){
                        return true;
                    }else{
                        return false;
                    }
                }, this);
                this.showPaymentField = ko.computed(function () {
                    if((this.saveCardConfig === "0") || !this.isSelectCard()){
                        return true;
                    }
                }, this);
                return this;
            },

            initStripe: function() {
                var self = this;
                this.loadStripeApi(function () {
                    var style = {
                        base: {
                            color: '#32325d',
                            lineHeight: '18px',
                            fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
                            fontSmoothing: 'antialiased',
                            fontSize: '16px',
                            '::placeholder': {
                                color: '#aab7c4'
                            }
                        },
                        invalid: {
                            color: '#fa755a',
                            iconColor: '#fa755a'
                        }
                    };

                    elements = stripe.elements();
                    card = elements.create('card', {
                        style: style,
                        hidePostalCode: true,
                        value: {
                            //postalCode: quote.billingAddress().postcode
                        }
                    });

                    card.mount('#'+self.getCode()+'-card-element');
                    card.addEventListener('change', function(event) {
                        var displayError = document.getElementById(self.getCode()+'-card-errors');
                        if (event.error) {
                            displayError.textContent = event.error.message;
                        } else {
                            displayError.textContent = '';
                        }
                    });
                });
            },
            loadStripeApi: function (callback) {
                var script = document.createElement('script');
                script.onload = function () {
                    stripe = Stripe(window.checkoutConfig.payment.magenest_stripe_config.publishableKey);
                    callback();
                };
                script.onerror = function (response) {
                    console.log("stripe js v3 load error");
                    console.log(response);
                };
                script.src = "https://js.stripe.com/v3/";
                document.head.appendChild(script);
            },

            placeOrder: function (data, event) {
                if (event) {
                    event.preventDefault();
                }
                var self  = this;
                var address = quote.billingAddress();
                var firstName = quote.billingAddress().firstname;
                var lastName = quote.billingAddress().lastname;
                var ownerInfo = {
                    owner: {
                        name: firstName +" "+ lastName,
                        address: {
                            line1: address.street[0],
                            line2: address.street[1],
                            city: address.city,
                            postal_code: address.postcode,
                            country: address.countryId,
                            state: address.region
                        },
                        email: (customer.customerData.email===null)?quote.guestEmail:customer.customerData.email
                    }
                };
                if (address.telephone) {
                    ownerInfo.owner.phone = address.telephone;
                }

                if (this.validate() && additionalValidators.validate()) {
                    self.isPlaceOrderActionAllowed(false);
                    if (!self.isSelectCard()) {
                        stripe.createSource(card, ownerInfo).then(function (result) {
                            if (result.error) {
                                self.isPlaceOrderActionAllowed(true);
                                var errorElement = document.getElementById(self.getCode() + '-card-errors');
                                errorElement.textContent = result.error.message;
                            } else {
                                self.source = result.source;
                                self.realPlaceOrder();
                            }
                        });
                    }else{
                        self.realPlaceOrder();
                    }
                }
            },

            realPlaceOrder: function () {
                var self = this;
                this.getPlaceOrderDeferredObject()
                    .fail(
                        function () {
                            self.isPlaceOrderActionAllowed(true);
                        }
                    ).done(
                    function () {
                        self.afterPlaceOrder();

                        if (self.redirectAfterPlaceOrder) {
                            redirectOnSuccessAction.execute();
                        }
                    }
                );
            },

            afterPlaceOrder: function () {
                var self = this;
                $.post(
                    url.build("stripe/checkout/threedSecure"),
                    {
                        form_key: $.cookie('form_key')
                    },
                    function (response) {
                        if (response.success) {
                            if(response.defaultPay){
                                redirectOnSuccessAction.execute();
                            }
                            if(response.threeDSercueActive){
                                window.location = response.threeDSercueUrl;
                            }

                        }
                        if (response.error){
                            self.isPlaceOrderActionAllowed(true);
                            console.log(response);
                            self.messageContainer.addErrorMessage({
                                message: response.message
                            });
                        }
                    },
                    "json"
                );
            },

            initialize: function () {
                var self = this;
                this._super();
            },

            getData: function() {
                var self = this;
                return {
                    'method': this.item.method,
                    'additional_data': {
                        'saved': this.saveCardOption,
                        'cardId': this.cardId(),
                        "stripe_response": JSON.stringify(self.source)
                    }
                }
            },

            getCode: function() {
                return 'magenest_stripe';
            },

            isActive: function() {
                return true;
            },

            validate: function() {
                var self = this;
                if(window.checkoutConfig.payment.magenest_stripe_config.publishableKey===""){
                    self.messageContainer.addErrorMessage({
                        message: "No API key provided."
                    });
                    return false;
                }
                if (typeof Stripe === "undefined"){
                    self.messageContainer.addErrorMessage({
                        message: "Stripe js load error"
                    });
                    return false;
                }

                return true;
            },

            getInstructions: function () {
                return window.checkoutConfig.payment.magenest_stripe.instructions;
            }
        });

    }
);