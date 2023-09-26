// app/design/frontend/[Vendor]/[Theme]/Magento_Checkout/web/js/view/shipping.js

define([
    'jquery',
    'mage/validation'
], function ($) {
    'use strict';

    return function (target) {
        return target.extend({
            validateEmailConfirmation: function () {
                var email = this.source.get('checkoutProvider').get('shippingAddress.email');
                var emailConfirmation = this.source.get('checkoutProvider').get('shippingAddress.email_confirmation');

                if (email !== emailConfirmation) {
                    this.source.set('params.invalid', true);
                    this.source.trigger('shippingAddress.data.validate');
                }
            }
        });
    }
});
