/**
* Copyright 2018 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

define(
    [
        'jquery',
        'ko',
        'Magento_Ui/js/form/form',
        'Aheadworks_OneStepCheckout/js/model/checkout-data',
        'Magento_Checkout/js/action/select-shipping-method',
        'Magento_Checkout/js/model/shipping-service',
        'Magento_Checkout/js/model/quote',
        'Aheadworks_OneStepCheckout/js/action/set-shipping-information',
        'Magento_Checkout/js/model/payment-service',
        'Magento_Checkout/js/model/payment/method-converter',
        'Aheadworks_OneStepCheckout/js/model/payment-methods-service',
        'Aheadworks_OneStepCheckout/js/model/totals-service',
        'Aheadworks_OneStepCheckout/js/model/checkout-section/cache-key-generator',
        'Aheadworks_OneStepCheckout/js/model/checkout-section/cache',
        'Aheadworks_OneStepCheckout/js/model/checkout-data-completeness-logger',
        'mage/translate'
    ],
    function (
        $,
        ko,
        Component,
        checkoutData,
        selectShippingMethodAction,
        shippingService,
        quote,
        setShippingInformationAction,
        paymentService,
        paymentMethodConverter,
        paymentMethodsService,
        totalsService,
        cacheKeyGenerator,
        cacheStorage,
        completenessLogger,
        $t
    ) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Aheadworks_OneStepCheckout/shipping-method'
            },
            rates: shippingService.getShippingRates(),
            isShown: ko.computed(function () {
                return !quote.isQuoteVirtual();
            }),
            isLoading: shippingService.isLoading,
            isSelected: ko.computed(function () {
				console.log('kumar');
                    return quote.shippingMethod() ?
                    quote.shippingMethod().carrier_code + '_' + quote.shippingMethod().method_code
                        : null;
                }
            ),
            errorValidationMessage: ko.observable(''),

            /**
             * @inheritdoc
             */
            initialize: function () {
				
                this._super();

                quote.shippingMethod.subscribe(function () {
                    this.errorValidationMessage('');
                }, this);
                completenessLogger.bindField('shippingMethod', quote.shippingMethod);

                return this;
            },

            /**
             * Select shipping method
             *
             * @param {Object} shippingMethod
             * @return {Boolean}
             */
           	 selectShippingMethod: function (shippingMethod) {
				 
//console.log(shippingMethod);
			if(shippingMethod.carrier_code == "storepickup"){ 
				 
				$(".del_pop").show();
				$(".close").click(function(){
					$(".del_pop").hide();
				});
				$(".submit-pop").click(function(){
					
					var isCheckedc = false;
					$(".available_stock").each(function(){					
						if($(this).prop('checked')){
							isCheckedc = true;							
						}
						
					});
					if(!isCheckedc){						
						return false;
					}
					$(".del_pop").hide();
				});
				$(".available_stock").on('change', function() {
					var optn = this.value;
					var base_url = window.location.origin+"/deliveryship/index";
					$.ajax({
						url: base_url, 
						type: "POST",
						showLoader: true, 
						data: {ship : shippingMethod, option : optn},
						success: function(result){
							//alert(result);
							$(".address_stor").html(result);
						}
					});
				});
				$(".submit-pop").click(function(){
					var isChecked = false;
					$(".available_stock").each(function(){					
						if($(this).prop('checked')){
							isChecked = true;							
						}
						
					});
					if(!isChecked){
						alert('Please choose store!');
						return false;
					}
					//var optn = $(".available_stock").val();
var optn = $("input[name='available_stock']:checked").val();
var optn_val = $("input[name='available_stock']:checked").attr("textval");
//alert(optn_val);
//alert(optn);
                                        var base_url = window.location.origin+"/deliveryship/index/select";
                                        $.ajax({
                                                url: base_url,
                                                type: "POST",
                                                showLoader: true,
                                                data: {ship : shippingMethod, option : optn},
                                                success: function(result){
                                                $(".aw-onestep-sidebar-totals .mark .value").html("(Pickup - "+optn_val+")");   
                                                //     alert(result);
                                                }
					});
				});
			}else{
				$(".aw-onestep-sidebar-totals .mark .value").html("("+shippingMethod.carrier_title+" - "+shippingMethod.method_title+")");
			}
                selectShippingMethodAction(shippingMethod);
                checkoutData.setSelectedShippingRate(
                    shippingMethod.carrier_code + '_' + shippingMethod.method_code
                );
                paymentMethodsService.isLoading(true);
                totalsService.isLoading(true);
                setShippingInformationAction().done(
                    function (response) {
                        var methods = paymentMethodConverter(response.payment_methods),
                            cacheKey = cacheKeyGenerator.generateCacheKey({
                                shippingAddress: quote.shippingAddress(),
                                billingAddress: quote.billingAddress(),
                                totals: quote.totals()
                            });

                        quote.setTotals(response.totals);
                        paymentService.setPaymentMethods(methods);
                        cacheStorage.set(
                            cacheKey,
                            {'payment_methods': methods, 'totals': response.totals}
                        );
                    }
                ).always(
                    function () {
                        paymentMethodsService.isLoading(false);
                        totalsService.isLoading(false);
                    }
                );
 
                return true;
            },

            /**
             * @inheritdoc
             */
            validate: function () {
				var isChecked = false;
					$(".available_stock").each(function(){					
						if($(this).prop('checked')){
							isChecked = true;							
						}						
					}); 
				if(!isChecked && $('#s_method_storepickup_storepickup').prop("checked")){
					$('#s_method_storepickup_storepickup').click(); 
					 this.errorValidationMessage($t('Please choose a store.'));
                    this.source.set('params.invalid', true);
				}
                if ((!quote.shippingMethod() && !quote.isQuoteVirtual()) || !$("#uncheckedvalidation").val()) {
                    this.errorValidationMessage($t('Please specify a shipping method.'));
                    this.source.set('params.invalid', true);
                }
            }
        }); 
    }
);
