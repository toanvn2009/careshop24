/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category  BSS
 * @package   Bss_OneStepCheckout
 * @author    Extension Team
 * @copyright Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license   http://bsscommerce.com/Bss-Commerce-License.txt
 */

define([
    'ko',
    'jquery',
    'uiComponent',
    'uiRegistry',
    'mage/translate',
    'Magento_Checkout/js/model/quote',
    'Bss_OneStepCheckout/js/model/shipping-rate-processor/new-address',
    'Bss_OneStepCheckout/js/model/shipping-rate-processor/customer-address',
    'Bss_OneStepCheckout/js/action/validate-shipping-information',
    'Bss_OneStepCheckout/js/action/validate-gift-wrap-before-order',
    'Magento_Checkout/js/model/full-screen-loader',
    'Magento_Checkout/js/action/select-billing-address',
    'Magento_Checkout/js/model/payment/additional-validators',
    'Magento_Checkout/js/model/shipping-service',
    'Magento_Checkout/js/model/shipping-rate-registry',
    'underscore',
    'Magento_Ui/js/modal/alert',
    'Magento_Checkout/js/checkout-data',
    'Magento_Checkout/js/model/totals',
    'Amazon_Payment/js/model/storage'
], function (
    ko,
    $,
    Component,
    registry,
    $t,
    quote,
    bssDefaultProcessor,
    bssCustomerAddressProcessor,
    validateShippingInformationAction,
    validateGiftWrapAction,
    fullScreenLoader,
    selectBillingAddress,
    additionalValidators,
    shippingService,
    rateRegistry,
    _,
    alert,
    checkoutData,
    totals,
    amazonStorage
) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Bss_OneStepCheckout/place-order-btn'
        },

        placeOrderLabel: ko.observable($t('Buy Now')),

        isVisible: ko.observable(true),

        isPlaceOrderActionAllowed: ko.observable(quote.billingAddress() != null && quote.paymentMethod() != null),

        /** @inheritdoc */
        initialize: function () {
            window.isPlaceOrderDispatched = false;
            this._super();
            var self = this;
            quote.billingAddress.subscribe(function (address) {
                if (quote.isVirtual()) {
                    setTimeout(function () {
                        self.isPlaceOrderActionAllowed(address !== null && quote.paymentMethod() != null);
                    }, 500);
                } else {
                    self.isPlaceOrderActionAllowed(address !== null && quote.paymentMethod() != null && quote.shippingMethod() != null);
                }
            }, this);
            quote.paymentMethod.subscribe(function (newMethod) {
                if (quote.isVirtual()) {
                    self.isPlaceOrderActionAllowed(newMethod !== null && quote.billingAddress() != null);
                } else {
                    self.isPlaceOrderActionAllowed(newMethod !== null && quote.billingAddress() != null && quote.shippingMethod() != null);
                }

                if ((newMethod.method === "amazonlogin" && !amazonStorage.isAmazonAccountLoggedIn()) ||
                    newMethod.method === "braintree_paypal"
                ) {
                    self.isVisible(false);
                }
            }, this);
            if (!quote.isVirtual()) {
                quote.shippingMethod.subscribe(function (method) {
                    var availableRate,
                        shippingRates = shippingService.getShippingRates();
                    if (method) {
                        availableRate = _.find(shippingRates(), function (rate) {
                            return rate['carrier_code'] + '_' + rate['method_code'] === method['carrier_code'] + '_' + method['method_code'];
                        });
                    }
                    self.isPlaceOrderActionAllowed(availableRate && quote.paymentMethod() != null && quote.billingAddress() != null);
                }, this);
            }

            if (window.checkoutConfig.magento_version >= "2.3.1" &&
                (window.checkoutConfig.paypal_in_context == true || !_.isEmpty(window.checkoutConfig.amazonLogin))
            ) {
                var selectedPaymentMethod = checkoutData.getSelectedPaymentMethod();

                if (selectedPaymentMethod == "paypal_express" ||
                    selectedPaymentMethod == "amazonlogin" ||
                    selectedPaymentMethod == "braintree_paypal") {
                    self.isVisible(false);
                }

                $(document).on('change', '.payment-method .radio', function () {
                    if ($('.payment-method._active').find('.actions-toolbar').is('#paypal-express-in-context-button') ||
                        ($(this).attr('id') === 'amazonlogin') ||
                        ($(this).attr('id') === 'braintree_paypal')
                    ) {
                        self.isVisible(false);
                    } else {
                        self.isVisible(true);
                    }
                });

               
            }
        },

        placeOrder: function (data, event) {
            var self = this;
            var shippingAddressComponent = registry.get('checkout.steps.shipping-step.shippingAddress');
            window.isPlaceOrderDispatched = true;
            if (event) {
                event.preventDefault();
            }
            if (additionalValidators.validate()) {
                if (quote.isVirtual()) {
                    $('input#' + self.getCode())
                        .closest('.payment-method').find('.payment-method-content .actions-toolbar:not([style*="display: none"]) button.action.checkout')
                        .trigger('click');
                } else {
                    if (shippingAddressComponent.validateShippingInformation()) {
                        var reSelectShippingAddress = false;
                        if (typeof window.shippingAddress !== "undefined" || !$.isEmptyObject(window.shippingAddress)) {
                            quote.shippingAddress(window.shippingAddress);
                            var type = quote.shippingAddress().getType();
                            if (type == 'customer-address') {
                                var cache = rateRegistry.get(quote.shippingAddress().getKey());
                                if (!cache) {
                                    reSelectShippingAddress = true;
                                    bssCustomerAddressProcessor(quote.shippingAddress()).done(function (result) {
                                        self.placeOrderContinue();
                                    }).fail(function (response) {

                                    }).always(function () {
                                        window.shippingAddress = {};
                                    });
                                } else {
                                    window.shippingAddress = {};
                                }
                            } else {
                                var cache = rateRegistry.get(quote.shippingAddress().getCacheKey());
                                if (!cache) {
                                    reSelectShippingAddress = true;
                                    bssDefaultProcessor(quote.shippingAddress()).done(function (result) {
                                        self.placeOrderContinue();
                                    }).fail(function (response) {

                                    }).always(function () {
                                        window.shippingAddress = {};
                                    });
                                } else {
                                    window.shippingAddress = {};
                                }
                            }
                        }
                        if (!reSelectShippingAddress) {
                            self.placeOrderContinue();
                        }
                    }
                }
            }
            return false;
        },

        placeOrderContinue: function () {
            var self = this;
            var billingAddressComponent = registry.get('checkout.steps.billing-step.payment.payments-list.billing-address-form-shared');

            if (billingAddressComponent.isAddressSameAsShipping()) {
                fullScreenLoader.startLoader();
                selectBillingAddress(quote.shippingAddress());
            }
            validateShippingInformationAction().done(
                function () {
                    var action = 0;
                    if ($('#giftwrap-checkbox input[name="giftwrap"]').is(":checked")) {
                        action = 1;
                    }
                    var fee = $('.giftwrap .amount .price').attr('amount');
                    if (typeof fee === "undefined") {
                        fee = 0;
                    }
                    fee = parseFloat(fee);
                    if (fee < 0) {
                        fee = 0;
                    }
                    validateGiftWrapAction(fee, action).done(
                        function (response) {
                            var res = JSON.parse(response);
                            if (res.status == false || res.gift_wrap_update == true || (res.gift_wrap_display == false && $('#giftwrap-checkbox').length)) {
                                return;
                            }
                            fullScreenLoader.stopLoader();
                            totals.isLoading(false);
                            $('input#' + self.getCode())
                                .closest('.payment-method').find('.payment-method-content .actions-toolbar:not([style*="display: none"]) button.action.checkout')
                                .trigger('click');
                        }
                    );
                }
            ).fail(
                function () {
                    fullScreenLoader.stopLoader();
                }
            );
        },

        getCode: function () {
            return quote.paymentMethod().method;
        }
    });
});