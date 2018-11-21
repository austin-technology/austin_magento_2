/*jshint browser:true jquery:true*/
/*global alert*/
define(
    [
        'jquery',
        'uiComponent',
        'underscore',
        'ko'
    ],
    function ($, Component, _, ko) {
        'use strict';

        return Component.extend({
            cardId: ko.observable(""),
            customerCard: ko.observableArray(""),
            showSelectCard: ko.observable(false),
            useCCField: ko.observable(true),

            initObservable: function () {
                var self = this;
                this._super();
                // this.observe([
                //     'b'
                // ]);
                // this.a = ko.computed(function () {
                //
                // });
                this.cardId.subscribe(function (value) {
                    if(typeof value !== 'undefined'){
                        self.useCCField(false);
                    }else{
                        self.useCCField(true);
                    }
                });
                return this;
            },

            getSaveCardData: function () {
                var self = this;
                $.post(
                    this.getCardUrl,
                    {'customer_id': window.stripe_customer_id},
                    function (response) {
                        if(response.success){
                            if(response.listCard.length > 0){
                                self.showSelectCard(true);
                            }
                            self.customerCard(response.listCard);
                        }
                    },
                    "json"
                );
            }

        });
    }
);