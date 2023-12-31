/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
 define([
 	'jquery',
 	'mage/translate',
 	'jquery/ui',
 	'Magento_Catalog/js/catalog-add-to-cart'
 	], function($, $t) {
 		"use strict";
		
    $.widget('mage.catalogAddToCart', {

        options: {
            processStart: null,
            processStop: null,
            bindSubmit: true,
            minicartSelector: '[data-block="minicart"]',
            messagesSelector: '[data-placeholder="messages"]',
            productStatusSelector: '.stock.available',
            addToCartButtonSelector: '.action.tocart',
            addToCartButtonDisabledClass: 'disabled',
            addToCartButtonTextWhileAdding: $t('Adding...'),
            addToCartButtonTextAdded: $t('Added'),
            addToCartButtonTextDefault: $t('Add to Cart')

        },

        _create: function() {
            if (this.options.bindSubmit) {
                this._bindSubmit();
            }
        },

        _bindSubmit: function() {
            var self = this;
            this.element.on('submit', function(e) {
                e.preventDefault();
                self.submitForm($(this));
            });
        },

        isLoaderEnabled: function() {
            return this.options.processStart && this.options.processStop;
        },

        submitForm: function(form) {
            var self = this;
			if ($('.container-swatch-opt-configurable-chair').length) {
				var selected_chair = true;
				$(".swatch-attribute-selected-option-chair").each(function() {
					var text = $(this).text();
					if (!text) {
						$(this).closest('a').addClass('not-option');
						selected_chair = false;
					}
				});
				if (!selected_chair) {
					return false;
				}
			}
			if ($('.block-bundle-customization-functional-boxer').length) {
				var selected_boxer = true;
				if (!$(".swatch-attribute-selected-option-custom-functional-boxer-size").text()) {
					$(".swatch-attribute-selected-option-custom-functional-boxer-size").closest('a').addClass('not-option');
					selected_boxer = false;
				}
				$(".custom-visible-selection .swatch-attribute-selected-option").each(function() {
					var text = $(this).text();
					if (!text) {
						$(this).closest('a').addClass('not-option');
						selected_boxer = false;
					}
				});
				if (!selected_boxer) {
					return false;
				}
			}
            if (form.has('input[type="file"]').length && form.find('input[type="file"]').val() !== '') {
                self.element.off('submit');
                form.submit();
            } else {
                self.ajaxSubmit(form);
            }
        },

        ajaxSubmit: function(form) {
			var self = this;
			self.disableAddToCartButton(form);
			var url = form.attr('action');
			$.ajax({
				url: url,
				data: form.serialize(),
				type: 'post',
				dataType: 'json',
				beforeSend: function() {
					if (self.isLoaderEnabled()) {
						$('body').trigger(self.options.processStart);
					}
					$('body').append('<div id="add-to-cart-loading-ajax-common"><span></span></div>');
				},
				success: function(res) {
					if (self.isLoaderEnabled()) {
						$('body').trigger(self.options.processStop);
					}

					if (res.backUrl) {
						window.location = res.backUrl;
						return;
					}
					$(self.options.minicartSelector).trigger('contentUpdated');
					
					if (res.messages) {
						$(self.options.messagesSelector).html(res.messages);
					}
					if (res.minicart) {
						$(self.options.minicartSelector).replaceWith(res.minicart);
						$(self.options.minicartSelector).trigger('contentUpdated');
					}
					if (res.product && res.product.statusText) {
						$(self.options.productStatusSelector)
						.removeClass('available')
						.addClass('unavailable')
						.find('span')
						.html(res.product.statusText);
					}
					
					var width_window = $(window).width();
					if(width_window > 480){
						window.ajaxCartTransport = true;
					}
					else{
						$('body #add-to-cart-loading-ajax-common').remove();
						if(res.html){
							require(['jquery', 'rokanthemes/fancybox'], function($){
								$.fancybox({
									content: res.html,
									helpers: {
										overlay: {
											locked: false
										}
									}
								});
							});
						}
					}
					self.enableAddToCartButton(form);
				}
			});
		},

        disableAddToCartButton: function(form) {
            var addToCartButton = $(form).find(this.options.addToCartButtonSelector);
            addToCartButton.addClass(this.options.addToCartButtonDisabledClass);
            addToCartButton.attr('title', this.options.addToCartButtonTextWhileAdding);
            addToCartButton.find('span').text(this.options.addToCartButtonTextWhileAdding);
        },

        enableAddToCartButton: function(form) {
            var self = this,
                addToCartButton = $(form).find(this.options.addToCartButtonSelector);

            addToCartButton.find('span').text(this.options.addToCartButtonTextAdded);
            addToCartButton.attr('title', this.options.addToCartButtonTextAdded);

            setTimeout(function() {
                addToCartButton.removeClass(self.options.addToCartButtonDisabledClass);
                addToCartButton.find('span').text(self.options.addToCartButtonTextDefault);
                addToCartButton.attr('title', self.options.addToCartButtonTextDefault);
            }, 1000);
        }
    });

    return $.mage.catalogAddToCart;
});