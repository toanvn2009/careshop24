define([
    'jquery',
    'underscore',
    'mage/template',
    'mage/smart-keyboard-handler',
    'mage/translate',
    'priceUtils',
    'Magento_Customer/js/customer-data',
    'jquery-ui-modules/widget',
    'jquery/jquery.parsequery',
    'mage/validation/validation'
], function ($, _, mageTemplate, keyboardHandler, $t, priceUtils, customerData) {
    'use strict';

    /**
     * Extend form validation to support swatch accessibility
     */
    $.widget('mage.validation', $.mage.validation, {
        /**
         * Handle form with swatches validation. Focus on first invalid swatch block.
         *
         * @param {jQuery.Event} event
         * @param {Object} validation
         */
        listenFormValidateHandler: function (event, validation) {
            var swatchWrapper, firstActive, swatches, swatch, successList, errorList, firstSwatch;

            this._superApply(arguments);

            swatchWrapper = '.swatch-attribute-options';
            swatches = $(event.target).find(swatchWrapper);

            if (!swatches.length) {
                return;
            }

            swatch = '.swatch-attribute';
            firstActive = $(validation.errorList[0].element || []);
            successList = validation.successList;
            errorList = validation.errorList;
            firstSwatch = $(firstActive).parent(swatch).find(swatchWrapper);

            keyboardHandler.focus(swatches);

            $.each(successList, function (index, item) {
                $(item).parent(swatch).find(swatchWrapper).attr('aria-invalid', false);
            });

            $.each(errorList, function (index, item) {
                $(item.element).parent(swatch).find(swatchWrapper).attr('aria-invalid', true);
            });

            if (firstSwatch.length) {
                $(firstSwatch).focus();
            }
        }
    });

    /**
     * Render tooltips by attributes (only to up).
     * Required element attributes:
     *  - option-type (integer, 0-3)
     *  - option-label (string)
     *  - option-tooltip-thumb
     *  - option-tooltip-value
     *  - thumb-width
     *  - thumb-height
     */
    $.widget('mage.SwatchRendererTooltip', {
        options: {
            delay: 200,                             //how much ms before tooltip to show
            tooltipClass: 'swatch-option-tooltip'  //configurable, but remember about css
        },

        /**
         * @private
         */
        _init: function () {
            var $widget = this,
                $this = this.element,
                $element = $('.' + $widget.options.tooltipClass),
                timer,
                type = parseInt($this.attr('option-type'), 10),
                label = $this.attr('option-label'),
                thumb = $this.attr('option-tooltip-thumb'),
                value = $this.attr('option-tooltip-value'),
                width = $this.attr('thumb-width'),
                height = $this.attr('thumb-height'),
                $image,
                $title,
                $corner;

            if (!$element.length) {
                $element = $('<div class="' +
                    $widget.options.tooltipClass +
                    '"><div class="image"></div><div class="title"></div><div class="corner"></div></div>'
                );
                $('body').append($element);
            }

            $image = $element.find('.image');
            $title = $element.find('.title');
            $corner = $element.find('.corner');

            $this.hover(function () {
                if (!$this.hasClass('disabled')) {
                    timer = setTimeout(
                        function () {
                            var leftOpt = null,
                                leftCorner = 0,
                                left,
                                $window;

                            if (type === 2) {
                                // Image
                                $image.css({
                                    'background': 'url("' + thumb + '") no-repeat center', //Background case
                                    'background-size': 'initial',
                                    'width': width + 'px',
                                    'height': height + 'px'
                                });
                                $image.show();
                            } else if (type === 1) {
                                // Color
                                $image.css({
                                    background: value
                                });
                                $image.show();
                            } else if (type === 0 || type === 3) {
                                // Default
                                $image.hide();
                            }

                            $title.text(label);

                            leftOpt = $this.offset().left;
                            left = leftOpt + $this.width() / 2 - $element.width() / 2;
                            $window = $(window);

                            // the numbers (5 and 5) is magick constants for offset from left or right page
                            if (left < 0) {
                                left = 5;
                            } else if (left + $element.width() > $window.width()) {
                                left = $window.width() - $element.width() - 5;
                            }

                            // the numbers (6,  3 and 18) is magick constants for offset tooltip
                            leftCorner = 0;

                            if ($element.width() < $this.width()) {
                                leftCorner = $element.width() / 2 - 3;
                            } else {
                                leftCorner = (leftOpt > left ? leftOpt - left : left - leftOpt) + $this.width() / 2 - 6;
                            }

                            $corner.css({
                                left: leftCorner
                            });
                            $element.css({
                                left: left,
                                top: $this.offset().top - $element.height() - $corner.height() - 18
                            }).show();
                        },
                        $widget.options.delay
                    );
                }
            }, function () {
                $element.hide();
                clearTimeout(timer);
            });

            $(document).on('tap', function () {
                $element.hide();
                clearTimeout(timer);
            });

            $this.on('tap', function (event) {
                event.stopPropagation();
            });
        }
    });

    /**
     * Render swatch controls with options and use tooltips.
     * Required two json:
     *  - jsonConfig (magento's option config)
     *  - jsonSwatchConfig (swatch's option config)
     *
     *  Tuning:
     *  - numberToShow (show "more" button if options are more)
     *  - onlySwatches (hide selectboxes)
     *  - moreButtonText (text for "more" button)
     *  - selectorProduct (selector for product container)
     *  - selectorProductPrice (selector for change price)
     */
    $.widget('mage.SwatchRenderer', {
        options: {
            classes: {
                attributeClass: 'swatch-attribute',
                attributeLabelClass: 'swatch-attribute-label',
                attributeSelectedOptionLabelClass: 'swatch-attribute-selected-option',
                attributeOptionsWrapper: 'swatch-attribute-options',
                attributeInput: 'swatch-input',
                optionClass: 'swatch-option',
                selectClass: 'swatch-select',
                moreButton: 'swatch-more',
                loader: 'swatch-option-loading'
            },
            // option's json config
            jsonConfig: {},

            // swatch's json config
            jsonSwatchConfig: {},

            // selector of parental block of prices and swatches (need to know where to seek for price block)
            selectorProduct: '.product-info-main',

            // selector of price wrapper (need to know where set price)
            selectorProductPrice: '[data-role=priceBox]',

            //selector of product images gallery wrapper
            mediaGallerySelector: '[data-gallery-role=gallery-placeholder]',

            // selector of category product tile wrapper
            selectorProductTile: '.product-item',

            // number of controls to show (false or zero = show all)
            numberToShow: false,

            // show only swatch controls
            onlySwatches: false,

            // enable label for control
            enableControlLabel: true,

            // control label id
            controlLabelId: '',

            // text for more button
            moreButtonText: $t('More'),

            // Callback url for media
            mediaCallback: '',

            // Local media cache
            mediaCache: {},

            // Cache for BaseProduct images. Needed when option unset
            mediaGalleryInitial: [{}],

            // Use ajax to get image data
            useAjax: false,

            /**
             * Defines the mechanism of how images of a gallery should be
             * updated when user switches between configurations of a product.
             *
             * As for now value of this option can be either 'replace' or 'prepend'.
             *
             * @type {String}
             */
            gallerySwitchStrategy: 'replace',

            // whether swatches are rendered in product list or on product page
            inProductList: false,

            // sly-old-price block selector
            slyOldPriceSelector: '.sly-old-price',

            // tier prise selectors start
            tierPriceTemplateSelector: '#tier-prices-template',
            tierPriceBlockSelector: '[data-role="tier-price-block"]',
            tierPriceTemplate: '',
            // tier prise selectors end

            // A price label selector
            normalPriceLabelSelector: '.normal-price .price-label'
        },

        /**
         * Get chosen product
         *
         * @returns int|null
         */
        getProduct: function () {
            var products = this._CalcProducts();

            return _.isArray(products) ? products[0] : null;
        },

        /**
         * @private
         */
        _init: function () {
            // Don't render the same set of swatches twice
            if ($(this.element).attr('data-rendered')) {
                return;
            }
            $(this.element).attr('data-rendered', true);

            if (_.isEmpty(this.options.jsonConfig.images)) {
                this.options.useAjax = true;
                // creates debounced variant of _LoadProductMedia()
                // to use it in events handlers instead of _LoadProductMedia()
                this._debouncedLoadProductMedia = _.debounce(this._LoadProductMedia.bind(this), 500);
            }

            if (this.options.jsonConfig !== '' && this.options.jsonSwatchConfig !== '') {
                // store unsorted attributes
                this.options.jsonConfig.mappedAttributes = _.clone(this.options.jsonConfig.attributes);
                this._sortAttributes();
                this._RenderControls();
                this._setPreSelectedGallery();
                $(this.element).trigger('swatch.initialized');
            } else {
                console.log('SwatchRenderer: No input data received');
            }
            this.options.tierPriceTemplate = $(this.options.tierPriceTemplateSelector).html();
        },

        /**
         * @private
         */
        _sortAttributes: function () {
            this.options.jsonConfig.attributes = _.sortBy(this.options.jsonConfig.attributes, function (attribute) {
                return parseInt(attribute.position, 10);
            });
        },

        /**
         * @private
         */
        _create: function () {
            var options = this.options,
                gallery = $('[data-gallery-role=gallery-placeholder]', '.column.main'),
                productData = this._determineProductData(),
                $main = productData.isInProductView ?
                    this.element.parents('.column.main') :
                    this.element.parents('.product-item-info');

            if (productData.isInProductView) {
                gallery.data('gallery') ?
                    this._onGalleryLoaded(gallery) :
                    gallery.on('gallery:loaded', this._onGalleryLoaded.bind(this, gallery));
            } else {
                options.mediaGalleryInitial = [{
                    'img': $main.find('.product-image-photo').attr('src')
                }];
            }

            this.productForm = this.element.parents(this.options.selectorProductTile).find('form:first');
            this.inProductList = this.productForm.length > 0;
        },

        /**
         * Determine product id and related data
         *
         * @returns {{productId: *, isInProductView: bool}}
         * @private
         */
        _determineProductData: function () {
            // Check if product is in a list of products.
            var productId,
                isInProductView = false;

            productId = this.element.parents('.product-item-details')
                    .find('.price-box.price-final_price').attr('data-product-id');

            if (!productId) {
                // Check individual product.
                productId = $('[name=product]').val();
                isInProductView = productId > 0;
            }

            return {
                productId: productId,
                isInProductView: isInProductView
            };
        },

        /**
         * Render controls
         *
         * @private
         */
        _RenderControls: function () {
            var $widget = this,
                container = this.element,
                classes = this.options.classes,
                chooseText = this.options.jsonConfig.chooseText,
                showTooltip = this.options.showTooltip,
                productData = this._determineProductData();

            $widget.optionsMap = {};

            if((productData.productId == 236 || productData.productId == 1029) && productData.isInProductView){
                var custom_color_chair = '<div class="custom-swatch-attribute-custom-chair custom-swatch-attribute-custom-chair-color"><div class="box-select-common-chair"><a href="#"><span class="swatch-attribute-label">'+$t("Color")+'</span><strong class="swatch-attribute-selected-option-chair"></strong></a></div></div>';
                container.append(custom_color_chair);
                container.append('<div style="display: none;" class="container-custom-swatch-attribute-grouped-common-attribute container-custom-swatch-attribute-grouped-common-attribute-color"></div>');
                
                var custom_size_chair = '<div class="custom-swatch-attribute-custom-chair custom-swatch-attribute-custom-chair-size"><div class="box-select-common-chair"><a href="#"><span class="swatch-attribute-label">'+$t("Size")+'</span><strong class="swatch-attribute-selected-option-chair"></strong></a></div></div>';
                container.append(custom_size_chair);
                container.append('<div style="display: none;" class="container-custom-swatch-attribute-grouped-common-attribute container-custom-swatch-attribute-grouped-common-attribute-size"></div>');
            }

            $.each(this.options.jsonConfig.attributes, function () {
                var item = this,
                    controlLabelId = 'option-label-' + item.code + '-' + item.id,
                    options = $widget._RenderSwatchOptions(item, controlLabelId),
                    select = $widget._RenderSwatchSelect(item, chooseText),
                    input = $widget._RenderFormInput(item),
                    listLabel = '',
                    label = '';

                var getcustomer = customerData.get('customer');
                var check_customer_login = false;
                if(getcustomer().fullname && getcustomer().firstname)
                {
                    check_customer_login = true;
                }

                // Show only swatch controls
                if ($widget.options.onlySwatches && !$widget.options.jsonSwatchConfig.hasOwnProperty(item.id)) {
                    return;
                }

                if ($widget.options.enableControlLabel) {
                    var attributeLabelTextFixed = item.label;
                    if((productData.productId == 236 || productData.productId == 1029) && item.code == 'color' && productData.isInProductView){
                        attributeLabelTextFixed = $t("Seat")+' '+attributeLabelTextFixed;
                    }
                    label +=
                        '<span id="' + controlLabelId + '" class="' + classes.attributeLabelClass + '">' +
                        $('<i></i>').text(attributeLabelTextFixed).html() +
                        '</span>' +
                        '<strong class="' + classes.attributeSelectedOptionLabelClass + '"></strong>';
                }

                if ($widget.inProductList) {
                    $widget.productForm.append(input);
                    input = '';
                    listLabel = 'aria-label="' + $('<i></i>').text(item.label).html() + '"';
                } else {
                    listLabel = 'aria-labelledby="' + controlLabelId + '"';
                }

                if(productData.isInProductView){
                    var id_code_fixed = 'suggest_'+item.code+'_'+item.id;
                    var other_idea = '<div class="container-submit-your-idea-this-product" style="display: none;">';
                        other_idea += '<div class="show-hide-container-suggest-on-following-product">';
                            other_idea += '<div class="left-container-suggest-on-following">';
                                other_idea += '<p class="title-suggest-on-following">'+$t("Suggest on the following")+':</p>';
                                other_idea += '<div class="container-suggest-on-following-options">';
                                    other_idea += '<div class="suggest-on-following-option-left">';
                                        other_idea += '<div class="cutom-fixed-checkbox-group"><input type="checkbox" name="color_'+id_code_fixed+'" id="color_'+id_code_fixed+'"> <label for="color_'+id_code_fixed+'">'+$t("Color")+'</label></div>';
                                        other_idea += '<div class="cutom-fixed-checkbox-group"><input type="checkbox" name="function_'+id_code_fixed+'" id="function_'+id_code_fixed+'"> <label for="function_'+id_code_fixed+'">'+$t("Function")+'</label></div>';
                                        other_idea += '<div class="cutom-fixed-checkbox-group"><input type="checkbox" name="other_'+id_code_fixed+'" id="other_'+id_code_fixed+'"> <label for="other_'+id_code_fixed+'">'+$t("Other")+'</label></div>';
                                    other_idea += '</div>';
                                    other_idea += '<div class="suggest-on-following-option-right">';
                                        other_idea += '<div class="cutom-fixed-checkbox-group"><input type="checkbox" name="size_'+id_code_fixed+'" id="size_'+id_code_fixed+'"> <label for="size_'+id_code_fixed+'">'+$t("Size")+'</label></div>';
                                        other_idea += '<div class="cutom-fixed-checkbox-group"><input type="checkbox" name="feature_'+id_code_fixed+'" id="feature_'+id_code_fixed+'"> <label for="feature_'+id_code_fixed+'">'+$t("Feature")+'</label></div>';
                                    other_idea += '</div>';
                                other_idea += '</div>';
                            other_idea += '</div>';
                            other_idea += '<div class="submit-your-idea-option-right">';
                                            if(!check_customer_login){
                                                other_idea += '<div class="container-content-your-idea-input" style=" padding-bottom: 10px;">';
                                                other_idea += '<label for="email_input_submit_'+id_code_fixed+'">'+$t("Your email")+':</label><input style="height: 4.4rem; border: 1px solid #cdcdcd; border-radius: 0; padding: 0 1rem; width: 100%" class="input-text" id="email_input_submit_'+id_code_fixed+'" name="email_input_submit_'+id_code_fixed+'">';
                                                other_idea += '</div>';
                                            }
                                other_idea += '<div class="container-content-your-idea-input">';
                                    other_idea += '<label for="textarea_submit_'+id_code_fixed+'">'+$t("Submit your idea on this product")+':</label><textarea id="textarea_submit_'+id_code_fixed+'" name="textarea_submit_'+id_code_fixed+'" rows="4" cols="50"></textarea>';
                                other_idea += '</div>';
                                other_idea += '<div class="button-fixed-container-join-us-win"><button type="button" value="'+$t("Join us & Win")+'">'+$t("Join us & Win")+'</button></div>';
                            other_idea += '</div>';
                        other_idea += '</div>';
                    other_idea += '</div>';
                    var looking_for = '<div class="you-are-looking-for-fixed"><a class="btn" href="#">'+$t("Haven't found what you are looking for ? ")+'</a></div>'+other_idea;
                    if(item.code == 'color' || item.code == 'base_color_material' || item.code == 'frame_color' || item.code == 'colors_glasses' || item.code == 'color_boardgame'|| item.code == 'flower_bags'){
                        // Create new control
                        if(productData.productId == 1107 || productData.productId == 1144 || productData.productId == 1146 || productData.productId == 1147 || productData.productId == 1145 || productData.productId == 1548){
                            looking_for = '';
                        }
                        var append_c_a =
                            '<div class="parent-container-common-show-hide-fixed ' + classes.attributeClass + ' ' + item.code + '" ' +
                                 'attribute-code="' + item.code + '" ' +
                                 'attribute-id="' + item.id + '"><div class="fixed-select-'+item.code+'-box box-select-common"><a href="#">' +
                                label +
                                '</a></div><div class="container-fixed-color-product-view-only container-show-hide-attribute-common fixed-style-type-image-swatch"><div aria-activedescendant="" ' +
                                     'tabindex="0" ' +
                                     'aria-invalid="false" ' +
                                     'aria-required="true" ' +
                                     'role="listbox" ' + listLabel +
                                     'class="' + classes.attributeOptionsWrapper + ' clearfix">' +
                                    options + select +
                                '</div>' + input + looking_for +
                            '</div></div>';

                        if((productData.productId == 236 || productData.productId == 1029) && productData.isInProductView){
                            container.find('.container-custom-swatch-attribute-grouped-common-attribute-color').append(append_c_a);
                        }
                        else{
                            container.append(append_c_a);
                        }
                    }
                    else{
                        // Create new control
                        if((productData.productId != 1107 && productData.productId != 1144 && productData.productId != 1146 && productData.productId != 1147 && productData.productId != 1145 && productData.productId != 1548) || item.code != 'size_boxer'){
                            if(productData.productId != 1548){
                                looking_for = '';
                            }
                            else{
                                if(item.code != 'size'){
                                    looking_for = '';
                                }
                                else{
                                    looking_for = '<div class="container-fixed-custom-qty-bundel-product"><div class="title-fixed-custom-qty-bundel-product">'+$t("Quantity")+'</div><div class="content-fixed-custom-qty-bundel-product"><a href="#" class="minus-custom-bundel-qty-fixed"><i class="fa fa-minus"></i></a><input class="input-custom-bundel-qty-fixed" name="custom_qty_bundel_'+id_code_fixed+'" type="text" value="1"><a href="#" class="plus-custom-bundel-qty-fixed"><i class="fa fa-plus"></i></a></div></div>'+looking_for;
                                }
                            }
                        }
                        var what_fits_size = '';
                        if(item.code == 'seat_sizes' || item.code == 'seat_height'){
                            what_fits_size = '<div class="fixed-seat-sizes-height-size-fits-me"><a href="#">'+$t("What size fits me?")+'</a></div>';
                        }
                        var append_s_a =
                            '<div class="parent-container-common-show-hide-fixed ' + classes.attributeClass + ' ' + item.code + '" ' +
                                 'attribute-code="' + item.code + '" ' +
                                 'attribute-id="' + item.id + '"><div class="fixed-select-'+item.code+'-box box-select-common"><a href="#">' +
                                label +
                                '</a></div><div class="container-fixed-'+item.code+'-product-view-only container-show-hide-attribute-common fixed-style-type-text-swatch"><div aria-activedescendant="" ' +
                                     'tabindex="0" ' +
                                     'aria-invalid="false" ' +
                                     'aria-required="true" ' +
                                     'role="listbox" ' + listLabel +
                                     'class="' + classes.attributeOptionsWrapper + ' clearfix">' +
                                    options + select +
                                '</div>' + input + looking_for + what_fits_size +
                            '</div></div>';

                        if((productData.productId == 236 || productData.productId == 1029) && productData.isInProductView){
                            container.find('.container-custom-swatch-attribute-grouped-common-attribute-size').append(append_s_a);
                        }
                        else{
                            container.append(append_s_a);
                        }
                    }
                }
                else{
                    // Create new control
                    container.append(
                        '<div class="' + classes.attributeClass + ' ' + item.code + '" ' +
                             'attribute-code="' + item.code + '" ' +
                             'attribute-id="' + item.id + '">' +
                            label +
                            '<div aria-activedescendant="" ' +
                                 'tabindex="0" ' +
                                 'aria-invalid="false" ' +
                                 'aria-required="true" ' +
                                 'role="listbox" ' + listLabel +
                                 'class="' + classes.attributeOptionsWrapper + ' clearfix">' +
                                options + select +
                            '</div>' + input +
                        '</div>'
                    );
                }

                $widget.optionsMap[item.id] = {};

                // Aggregate options array to hash (key => value)
                $.each(item.options, function () {
                    if (this.products.length > 0) {
                        $widget.optionsMap[item.id][this.id] = {
                            price: parseInt(
                                $widget.options.jsonConfig.optionPrices[this.products[0]].finalPrice.amount,
                                10
                            ),
                            products: this.products
                        };
                    }
                });
            });
            
            if (showTooltip === 1) {
                // Connect Tooltip
                container
                    .find('[option-type="1"], [option-type="2"], [option-type="0"], [option-type="3"]')
                    .SwatchRendererTooltip();
            }

            // Hide all elements below more button
            $('.' + classes.moreButton).nextAll().hide();

            // Handle events like click or change
            $widget._EventListener();

            // Rewind options
            $widget._Rewind(container);

            //Emulate click on all swatches from Request
            $widget._EmulateSelected($.parseQuery());
            $widget._EmulateSelected($widget._getSelectedAttributes());
        },

        /**
         * Render swatch options by part of config
         *
         * @param {Object} config
         * @param {String} controlId
         * @returns {String}
         * @private
         */
        _RenderSwatchOptions: function (config, controlId) {
            var optionConfig = this.options.jsonSwatchConfig[config.id],
                optionClass = this.options.classes.optionClass,
                sizeConfig = this.options.jsonSwatchImageSizeConfig,
                moreLimit = parseInt(this.options.numberToShow, 10),
                moreClass = this.options.classes.moreButton,
                moreText = this.options.moreButtonText,
                countAttributes = 0,
                productData = this._determineProductData(),
                optionPricesFixed = this.options.jsonConfig.optionPrices,
                html = '';

            if (!this.options.jsonSwatchConfig.hasOwnProperty(config.id)) {
                return '';
            }
            var options_count = config.options.length;
            $.each(config.options, function (index) {
                var id,
                    type,
                    value,
                    thumb,
                    label,
                    width,
                    height,
                    attr,
                    swatchImageWidth,
                    swatchImageHeight;

                if (!optionConfig.hasOwnProperty(this.id)) {
                    return '';
                }

                // Add more button
                if (moreLimit === countAttributes++) {
                    html += '<a href="#" class="' + moreClass + '"><span>' + moreText + '</span></a>';
                }

                id = this.id;
                type = parseInt(optionConfig[id].type, 10);
                value = optionConfig[id].hasOwnProperty('value') ?
                    $('<i></i>').text(optionConfig[id].value).html() : '';
                thumb = optionConfig[id].hasOwnProperty('thumb') ? optionConfig[id].thumb : '';
                width = _.has(sizeConfig, 'swatchThumb') ? sizeConfig.swatchThumb.width : 110;
                height = _.has(sizeConfig, 'swatchThumb') ? sizeConfig.swatchThumb.height : 90;
                label = this.label ? $('<i></i>').text(this.label).html() : '';
                attr =
                    ' id="' + controlId + '-item-' + id + '"' +
                    ' index="' + index + '"' +
                    ' aria-checked="false"' +
                    ' aria-describedby="' + controlId + '"' +
                    ' tabindex="0"' +
                    ' option-type="' + type + '"' +
                    ' option-id="' + id + '"' +
                    ' option-label="' + label + '"' +
                    ' aria-label="' + label + '"' +
                    ' option-tooltip-thumb="' + thumb + '"' +
                    ' option-tooltip-value="' + value + '"' +
                    ' role="option"' +
                    ' thumb-width="' + width + '"' +
                    ' thumb-height="' + height + '"';

                swatchImageWidth = _.has(sizeConfig, 'swatchImage') ? sizeConfig.swatchImage.width : 30;
                swatchImageHeight = _.has(sizeConfig, 'swatchImage') ? sizeConfig.swatchImage.height : 20;

                if (!this.hasOwnProperty('products') || this.products.length <= 0) {
                    attr += ' option-empty="true"';
                }
                if (index > 2 ) {
                    attr += ' hide-swacth';
                }
                if(productData.isInProductView){
                    var myObjColorCode = [
                        {"name" : "Black", "code" : "BLK"},
                        {"name" : "Grey", "code" : "GRY"},
                        {"name" : "Iceblue", "code" : "IBE"},
                        {"name" : "Olive", "code" : "OLE"},
                        {"name" : "Orage", "code" : "ORE"},
                        {"name" : "Red", "code" : "RED"},
                        {"name" : "Green", "code" : "GRE"},
                        {"name" : "Skyblue", "code" : "SBE"},
                        {"name" : "Springgreen", "code" : "SRN"},
                        {"name" : "Yellow", "code" : "YLW"},
                        {"name" : "Black Matt", "code" : "BLM"},
                        {"name" : "Blue Matt", "code" : "BMT"},
                        {"name" : "Green Matt", "code" : "GMT"},
                        {"name" : "Red Shiny", "code" : "RSY"},
                        {"name" : "White Shiny", "code" : "WYS"},
                        {"name" : "Yellow Shiny", "code" : "YLS"},
                        {"name" : "White", "code" : "WHT"},
                        {"name" : "Anthracite", "code" : "ATC"},
                        {"name" : "Blue", "code" : "BLU"},
                        {"name" : "Yellow Green", "code" : "YLG"},
                        {"name" : "Silver", "code" : "SLR"},
                        {"name" : "Costa", "code" : "CTA"},
                        {"name" : "Curacao", "code" : "CRO"},
                        {"name" : "Demerera", "code" : "DMA"},
                        {"name" : "Domingo", "code" : "DMO"},
                        {"name" : "Guava", "code" : "GAA"},
                        {"name" : "Havana", "code" : "HNA"},
                        {"name" : "Montserat", "code" : "MNT"},
                        {"name" : "Paradise", "code" : "PDE"},
                        {"name" : "Sombrero", "code" : "STL"},
                        {"name" : "Steel", "code" : "SEL"},
                        {"name" : "Jet Black", "code" : "JBL"},
                        {"name" : "Lily White", "code" : "LWH"},
                        {"name" : "Pearl Gray", "code" : "PGR"},
                        {"name" : "Alu black polished", "code" : "ABP"},
                        {"name" : "Alu matt", "code" : "AMT"},
                        {"name" : "Alu matt black", "code" : "AMB"},
                        {"name" : "Alu polished", "code" : "APD"},
                        {"name" : "Wood bamboo", "code" : "WBO"},
                        {"name" : "Blue Matt Alu", "code" : "BMA"},
                        {"name" : "DEMI Dark Brown Matt", "code" : "DDM"},
                        {"name" : "Matt Black Metal", "code" : "MBM"},
                        {"name" : "Matt Gun Metal Grey", "code" : "MGG"},
                        {"name" : "Matt Pearl White", "code" : "MPW"},
                        {"name" : "Shiny Alu Orange", "code" : "SAO"},
                        {"name" : "Shiny Red", "code" : "SRD"},
                        {"name" : "Shiny Yellow", "code" : "SYW"},
                        {"name" : "Fresh Bamboo", "code" : "FBO"}
                    ];

                    var color_code_string = '';
                    var color_code_attr = '';
                    for (var i_t_f in myObjColorCode) {
                        if(myObjColorCode[i_t_f].name == label){
                            color_code_string = '<span class="fixed-color-core-careshop-pro-detail">'+myObjColorCode[i_t_f].code+'</span>';
                            color_code_attr = ' data-color-code="'+myObjColorCode[i_t_f].code+'"';
                        }
                    }

                    if (type === 0) {
                        // Text
                        if(productData.productId == 1107 || productData.productId == 1144 || productData.productId == 1146 || productData.productId == 1147 || productData.productId == 1145 || productData.productId == 1548){
                            if(controlId == 'option-label-gender_boxer-171'){
                                html += '<div class="container-border-fixed-common-all"><div class="' + optionClass + ' text" ' + attr + '>' + (value ? value : label) +
                                '</div><span class="label-fixed-swatch-gender">'+label+'</span><span class="border-fixed-common-all"></span></div>';
                            }
                            else{
                                html += '<div class="container-border-fixed-common-all"><div class="' + optionClass + ' text fixed-size-group-foreach" ' + attr + '>' + (value ? value : label) +
                                '</div><span class="border-fixed-common-all"></span></div>';
                            }
                        }
                        else{
                            if(controlId == 'option-label-seat_sizes-161' || controlId == 'option-label-seat_height-162'){
                                var des_fixed = '';
                                var strArray = label.split(" ");
                                for(var isf = 0; isf < strArray.length; isf++){
                                    des_fixed = des_fixed + "<span>" + strArray[isf] + " </span>";
                                }

                                html += '<div class="container-seat-sizes-height-fixed-common-all"><div class="' + optionClass + ' text" ' + attr + '>' + (value ? value : label) +
                                '</div><span class="container-label-price-fixed-seat-sizes-height"><span class="label-fixed-swatch-seat-sizes-height">'+des_fixed+'</span><span class="price-fixed-swatch-seat-sizes-height">+ '+priceUtils.formatPrice(0)+'</span></span><span class="border-fixed-common-seat-sizes-height"></span></div>';
                            }
                            else{
                                html += '<div class="' + optionClass + ' text" ' + attr + '>' + (value ? value : label) +
                                '</div>';
                            }
                        }
                    } else if (type === 1) {
                        // Color
                        var fixed_price_show = (optionPricesFixed[this.products[0]].finalPrice.amount) ? optionPricesFixed[this.products[0]].finalPrice.amount : 0;
                        if(controlId == 'option-label-frame_color-163' || controlId == 'option-label-base_color_material-164'){
                            fixed_price_show = 0;
                        }
                        html += '<div class="' + optionClass + ' color" ' + attr + color_code_attr +
                            ' style="background: ' + value +
                            ' no-repeat center; background-size: initial;">' + '' +
                            '</div><div class="container-name-price-fixed-swatch-detail"><span class="label-fixed-swatch-detail">'+label+'</span><span class="price-fixed-swatch-detail">'+priceUtils.formatPrice(fixed_price_show)+color_code_string+'</span></div>';
                    } else if (type === 2) {
                        // Image
                        var fixed_price_show = (optionPricesFixed[this.products[0]].finalPrice.amount) ? optionPricesFixed[this.products[0]].finalPrice.amount : 0;
                        if(controlId == 'option-label-frame_color-163' || controlId == 'option-label-base_color_material-164'){
                            fixed_price_show = 0;
                        }
                        html += '<div class="' + optionClass + ' image" ' + attr + color_code_attr +
                            ' style="background: url(' + value + ') no-repeat center; background-size: initial;width:' +
                            swatchImageWidth + 'px; height:' + swatchImageHeight + 'px">' + '' +
                            '</div><div class="container-name-price-fixed-swatch-detail"><span class="label-fixed-swatch-detail">'+label+'</span><span class="price-fixed-swatch-detail">'+priceUtils.formatPrice(fixed_price_show)+color_code_string+'</span></div>';
                    } else if (type === 3) {
                        // Clear
                        html += '<div class="' + optionClass + '" ' + attr + '></div>';
                    } else {
                        // Default
                        html += '<div class="' + optionClass + '" ' + attr + '>' + label + '</div>';
                    }
                }
                else{
                    if (type === 0) {
                    // Text
                        html += '<div class="' + optionClass + ' text" ' + attr + '>' + (value ? value : label) +
                            '</div>';
                    } else if (type === 1) {
                        // Color
                        html += '<div class="' + optionClass + ' color" ' + attr +
                            ' style="background: ' + value +
                            ' no-repeat center; background-size: initial;">' + '' +
                            '</div>';
                    } else if (type === 2) {
                        // Image
                        html += '<div class="' + optionClass + ' image" ' + attr +
                            ' style="background: url(' + value + ') no-repeat center; background-size: initial;width:' +
                            swatchImageWidth + 'px; height:' + swatchImageHeight + 'px">' + '' +
                            '</div>';
                    } else if (type === 3) {
                        // Clear
                        html += '<div class="' + optionClass + '" ' + attr + '></div>';
                    } else {
                        // Default
                        html += '<div class="' + optionClass + '" ' + attr + '>' + label + '</div>';
                    }
                }
                if (index == 3 && (options_count-3) > 0) {
                    html += '<div class="show-mode-swatch">+'+ (options_count-3) +'</div>';
                }
            });

            return html;
        },

        /**
         * Render select by part of config
         *
         * @param {Object} config
         * @param {String} chooseText
         * @returns {String}
         * @private
         */
        _RenderSwatchSelect: function (config, chooseText) {
            var html;

            if (this.options.jsonSwatchConfig.hasOwnProperty(config.id)) {
                return '';
            }

            html =
                '<select class="' + this.options.classes.selectClass + ' ' + config.code + '">' +
                '<option value="0" option-id="0">' + chooseText + '</option>';

            $.each(config.options, function () {
                var label = this.label,
                    attr = ' value="' + this.id + '" option-id="' + this.id + '"';

                if (!this.hasOwnProperty('products') || this.products.length <= 0) {
                    attr += ' option-empty="true"';
                }

                html += '<option ' + attr + '>' + label + '</option>';
            });

            html += '</select>';

            return html;
        },

        /**
         * Input for submit form.
         * This control shouldn't have "type=hidden", "display: none" for validation work :(
         *
         * @param {Object} config
         * @private
         */
        _RenderFormInput: function (config) {
            return '<input class="' + this.options.classes.attributeInput + ' super-attribute-select" ' +
                'name="super_attribute[' + config.id + ']" ' +
                'type="text" ' +
                'value="" ' +
                'data-selector="super_attribute[' + config.id + ']" ' +
                'data-validate="{required: true}" ' +
                'aria-required="true" ' +
                'aria-invalid="false">';
        },

        /**
         * Event listener
         *
         * @private
         */
        _EventListener: function () {
            var $widget = this,
                options = this.options.classes,
                target;

            $widget.element.on('click', '.' + options.optionClass, function () {
                return $widget._OnClick($(this), $widget);
            });

            $widget.element.on('change', '.' + options.selectClass, function () {
                return $widget._OnChange($(this), $widget);
            });

            $widget.element.on('click', '.' + options.moreButton, function (e) {
                e.preventDefault();

                return $widget._OnMoreClick($(this));
            });

            $widget.element.on('keydown', function (e) {
                if (e.which === 13) {
                    target = $(e.target);

                    if (target.is('.' + options.optionClass)) {
                        return $widget._OnClick(target, $widget);
                    } else if (target.is('.' + options.selectClass)) {
                        return $widget._OnChange(target, $widget);
                    } else if (target.is('.' + options.moreButton)) {
                        e.preventDefault();

                        return $widget._OnMoreClick(target);
                    }
                }
            });
        },

        /**
         * Load media gallery using ajax or json config.
         *
         * @private
         */
        _loadMedia: function () {
            var $main = this.inProductList ?
                    this.element.parents('.product-item-info') :
                    this.element.parents('.column.main'),
                images;

            if (this.options.useAjax) {
                this._debouncedLoadProductMedia();
            }  else {
                images = this.options.jsonConfig.images[this.getProduct()];

                if (!images) {
                    images = this.options.mediaGalleryInitial;
                }
                this.updateBaseImage(this._sortImages(images), $main, !this.inProductList);
            }
        },

        /**
         * Sorting images array
         *
         * @private
         */
        _sortImages: function (images) {
            return _.sortBy(images, function (image) {
                return parseInt(image.position, 10);
            });
        },

        /**
         * Event for swatch options
         *
         * @param {Object} $this
         * @param {Object} $widget
         * @private
         */
        _OnClick: function ($this, $widget) {
            var $parent = $this.parents('.' + $widget.options.classes.attributeClass),
                $wrapper = $this.parents('.' + $widget.options.classes.attributeOptionsWrapper),
                $label = $parent.find('.' + $widget.options.classes.attributeSelectedOptionLabelClass),
                attributeId = $parent.attr('attribute-id'),
                $input = $parent.find('.' + $widget.options.classes.attributeInput),
                checkAdditionalData = JSON.parse(this.options.jsonSwatchConfig[attributeId]['additional_data']);

            if($('.container-swatch-opt-configurable-chair').length > 0){
                var count_item_attribute = $this.closest('.container-custom-swatch-attribute-grouped-common-attribute').find('.parent-container-common-show-hide-fixed').length;
                var selected_item_attribute = $this.closest('.container-custom-swatch-attribute-grouped-common-attribute').find('.parent-container-common-show-hide-fixed .swatch-option.selected').length;
                if ($this.hasClass('selected')) {
                    selected_item_attribute = selected_item_attribute - 1;
                } else {
                    selected_item_attribute = selected_item_attribute + 1;
                }
                if(count_item_attribute <= selected_item_attribute){
                    $('.custom-swatch-attribute-custom-chair').removeClass('actived');
                    $('.container-custom-swatch-attribute-grouped-common-attribute').removeClass('actived');
                    $('.container-custom-swatch-attribute-grouped-common-attribute').hide();
                }
            }
            else{
                $('.container-show-hide-attribute-common').removeClass('active');
                $('.box-select-common').removeClass('active');
            }

            if ($widget.inProductList) {
                $input = $widget.productForm.find(
                    '.' + $widget.options.classes.attributeInput + '[name="super_attribute[' + attributeId + ']"]'
                );
            }

            if ($this.hasClass('disabled')) {
                return;
            }

            if ($this.hasClass('selected')) {
                $parent.removeAttr('option-selected').find('.selected').removeClass('selected');
                $input.val('');
                $label.text('');
                $this.attr('aria-checked', false);
                if($this.attr('option-id') == '5524' || $this.attr('option-id') == '5525'){
                    $('.fixed-size-group-foreach').removeClass('added-disabled-size');
                    $('.container-border-fixed-common-all').removeClass('atc');
                }
            } else {
                $parent.attr('option-selected', $this.attr('option-id')).find('.selected').removeClass('selected');
                if($this.attr('data-color-code')){
                    $label.text($this.attr('option-label')+' '+'('+$this.attr('data-color-code')+')');
                    $label.closest('a').removeClass('not-option');
                }
                else{
                    $label.text($this.attr('option-label'));
                    $label.closest('a').removeClass('not-option');
                }
                $input.val($this.attr('option-id'));
                $input.attr('data-attr-name', this._getAttributeCodeById(attributeId));
                $this.addClass('selected');
                $widget._toggleCheckedAttributes($this, $wrapper);

                if($this.attr('option-id') == '5524'){
                    $('.fixed-size-group-foreach').removeClass('added-disabled-size');
                    $('.container-border-fixed-common-all').removeClass('atc');
                    $this.closest('.container-border-fixed-common-all').addClass('atc');
                    $('.fixed-size-group-foreach').each(function(index) {
                        if($(this).attr('option-id') == '5527' || $(this).attr('option-id') == '5526' || $(this).attr('option-id') == '5528'){
                            $(this).addClass('added-disabled-size');
                        }
                    });
                }
                
                if($this.attr('option-id') == '5525'){
                    $('.fixed-size-group-foreach').removeClass('added-disabled-size');
                    $('.container-border-fixed-common-all').removeClass('atc');
                    $this.closest('.container-border-fixed-common-all').addClass('atc');
                    $('.fixed-size-group-foreach').each(function(index) {
                        if($(this).attr('option-id') == '5529' || $(this).attr('option-id') == '5530' || $(this).attr('option-id') == '5531'){
                            $(this).addClass('added-disabled-size');
                        }
                    });
                }
            }

            $widget._Rebuild();

            if ($widget.element.parents($widget.options.selectorProduct)
                    .find(this.options.selectorProductPrice).is(':data(mage-priceBox)')
            ) {
                $widget._UpdatePrice();
            }

            $(document).trigger('updateMsrpPriceBlock',
                [
                    _.findKey($widget.options.jsonConfig.index, $widget.options.jsonConfig.defaultValues),
                    $widget.options.jsonConfig.optionPrices
                ]);

            if (parseInt(checkAdditionalData['update_product_preview_image'], 10) === 1) {
                $widget._loadMedia();
            }

            $input.trigger('change');
            if($('.swatch-attribute[attribute-code]').length && $('.swatch-attribute[option-selected]').length){
                if($('.swatch-attribute[attribute-code]').length == $('.swatch-attribute[option-selected]').length && $('#product-addtocart-button').length){
                    $('#product-addtocart-button').removeClass('disable-add-to-cart-product-button');
                    $('#product-addtocart-button').addClass('enable-add-to-cart-product-button');
                }
                else{
                    if($('#product-addtocart-button').length){
                        $('#product-addtocart-button').addClass('disable-add-to-cart-product-button');
                        $('#product-addtocart-button').removeClass('enable-add-to-cart-product-button');
                    }
                }
            }

            if($('.pro-detail-normal-price-ini-fixed').length && $('.pro-detail-normal-price-click-fixed').length){
                $('.pro-detail-normal-price-ini-fixed').hide();
                $('.pro-detail-normal-price-click-fixed').show();
            }

            if($('.container-swatch-opt-configurable-chair').length > 0){
                var selected_item_arr = [];
                if($this.closest('.container-custom-swatch-attribute-grouped-common-attribute').find('.parent-container-common-show-hide-fixed .swatch-option.selected').length > 0){
                    var selected_item_attribute_get_label = $this.closest('.container-custom-swatch-attribute-grouped-common-attribute').find('.parent-container-common-show-hide-fixed .swatch-option.selected');
                    selected_item_attribute_get_label.each(function(index) {
                        if($(this).attr('data-color-code')){
                            selected_item_arr[index] = $(this).attr('option-label')+'('+$(this).attr('data-color-code')+')';
                        }
                        else{
                            selected_item_arr[index] = $(this).attr('option-label');
                        }
                    });
                }
                if(selected_item_arr.length > 0){
                    var result_join_text = selected_item_arr.join(', ');
                    if($this.closest('.parent-container-common-show-hide-fixed').attr('attribute-code') == 'color' || $this.closest('.parent-container-common-show-hide-fixed').attr('attribute-code') == 'frame_color' || $this.closest('.parent-container-common-show-hide-fixed').attr('attribute-code') == 'base_color_material'){
                        $this.closest('.container-swatch-opt-configurable-chair').find('.custom-swatch-attribute-custom-chair-color .swatch-attribute-selected-option-chair').text(result_join_text);
                        $this.closest('.container-swatch-opt-configurable-chair').find('.custom-swatch-attribute-custom-chair-color .box-select-common-chair').addClass('selected');
                    }
                    else{
                        $this.closest('.container-swatch-opt-configurable-chair').find('.custom-swatch-attribute-custom-chair-size .swatch-attribute-selected-option-chair').text(result_join_text);
                        $this.closest('.container-swatch-opt-configurable-chair').find('.custom-swatch-attribute-custom-chair-size .box-select-common-chair').addClass('selected');
                    }
                }
                else{
                    if($this.closest('.parent-container-common-show-hide-fixed').attr('attribute-code') == 'color' || $this.closest('.parent-container-common-show-hide-fixed').attr('attribute-code') == 'frame_color' || $this.closest('.parent-container-common-show-hide-fixed').attr('attribute-code') == 'base_color_material'){
                        $this.closest('.container-swatch-opt-configurable-chair').find('.custom-swatch-attribute-custom-chair-color .swatch-attribute-selected-option-chair').text('');
                        $this.closest('.container-swatch-opt-configurable-chair').find('.custom-swatch-attribute-custom-chair-color .box-select-common-chair').removeClass('selected');
                    }
                    else{
                       $this.closest('.container-swatch-opt-configurable-chair').find('.custom-swatch-attribute-custom-chair-size .swatch-attribute-selected-option-chair').text('');
                       $this.closest('.container-swatch-opt-configurable-chair').find('.custom-swatch-attribute-custom-chair-size .box-select-common-chair').removeClass('selected');
                    }
                }
            }
        },

        /**
         * Get human readable attribute code (eg. size, color) by it ID from configuration
         *
         * @param {Number} attributeId
         * @returns {*}
         * @private
         */
        _getAttributeCodeById: function (attributeId) {
            var attribute = this.options.jsonConfig.mappedAttributes[attributeId];

            return attribute ? attribute.code : attributeId;
        },

        /**
         * Toggle accessibility attributes
         *
         * @param {Object} $this
         * @param {Object} $wrapper
         * @private
         */
        _toggleCheckedAttributes: function ($this, $wrapper) {
            $wrapper.attr('aria-activedescendant', $this.attr('id'))
                    .find('.' + this.options.classes.optionClass).attr('aria-checked', false);
            $this.attr('aria-checked', true);
        },

        /**
         * Event for select
         *
         * @param {Object} $this
         * @param {Object} $widget
         * @private
         */
        _OnChange: function ($this, $widget) {
            var $parent = $this.parents('.' + $widget.options.classes.attributeClass),
                attributeId = $parent.attr('attribute-id'),
                $input = $parent.find('.' + $widget.options.classes.attributeInput);

            if ($widget.productForm.length > 0) {
                $input = $widget.productForm.find(
                    '.' + $widget.options.classes.attributeInput + '[name="super_attribute[' + attributeId + ']"]'
                );
            }

            if ($this.val() > 0) {
                $parent.attr('option-selected', $this.val());
                $input.val($this.val());
            } else {
                $parent.removeAttr('option-selected');
                $input.val('');
            }

            $widget._Rebuild();
            $widget._UpdatePrice();
            $widget._loadMedia();
            $input.trigger('change');
        },

        /**
         * Event for more switcher
         *
         * @param {Object} $this
         * @private
         */
        _OnMoreClick: function ($this) {
            $this.nextAll().show();
            $this.blur().remove();
        },

        /**
         * Rewind options for controls
         *
         * @private
         */
        _Rewind: function (controls) {
            controls.find('div[option-id], option[option-id]').removeClass('disabled').removeAttr('disabled');
            controls.find('div[option-empty], option[option-empty]')
                .attr('disabled', true)
                .addClass('disabled')
                .attr('tabindex', '-1');
        },

        /**
         * Rebuild container
         *
         * @private
         */
        _Rebuild: function () {
            var $widget = this,
                controls = $widget.element.find('.' + $widget.options.classes.attributeClass + '[attribute-id]'),
                selected = controls.filter('[option-selected]');

            // Enable all options
            $widget._Rewind(controls);

            // done if nothing selected
            if (selected.length <= 0) {
                return;
            }

            // Disable not available options
            controls.each(function () {
                var $this = $(this),
                    id = $this.attr('attribute-id'),
                    products = $widget._CalcProducts(id);

                if (selected.length === 1 && selected.first().attr('attribute-id') === id) {
                    return;
                }

                $this.find('[option-id]').each(function () {
                    var $element = $(this),
                        option = $element.attr('option-id');

                    if (!$widget.optionsMap.hasOwnProperty(id) || !$widget.optionsMap[id].hasOwnProperty(option) ||
                        $element.hasClass('selected') ||
                        $element.is(':selected')) {
                        return;
                    }

                    if (_.intersection(products, $widget.optionsMap[id][option].products).length <= 0) {
                        $element.attr('disabled', true).addClass('disabled');
                    }
                });
            });
        },

        /**
         * Get selected product list
         *
         * @returns {Array}
         * @private
         */
        _CalcProducts: function ($skipAttributeId) {
            var $widget = this,
                products = [];

            // Generate intersection of products
            $widget.element.find('.' + $widget.options.classes.attributeClass + '[option-selected]').each(function () {
                var id = $(this).attr('attribute-id'),
                    option = $(this).attr('option-selected');

                if ($skipAttributeId !== undefined && $skipAttributeId === id) {
                    return;
                }

                if (!$widget.optionsMap.hasOwnProperty(id) || !$widget.optionsMap[id].hasOwnProperty(option)) {
                    return;
                }

                if (products.length === 0) {
                    products = $widget.optionsMap[id][option].products;
                } else {
                    products = _.intersection(products, $widget.optionsMap[id][option].products);
                }
            });

            return products;
        },

        /**
         * Update total price
         *
         * @private
         */
        _UpdatePrice: function () {
            var $widget = this,
                $product = $widget.element.parents($widget.options.selectorProduct),
                $productPrice = $product.find(this.options.selectorProductPrice),
                result = $widget._getNewPrices(),
                tierPriceHtml,
                isShow;

            $productPrice.trigger(
                'updatePrice',
                {
                    'prices': $widget._getPrices(result, $productPrice.priceBox('option').prices)
                }
            );

            isShow = typeof result != 'undefined' && result.oldPrice.amount !== result.finalPrice.amount;

            $product.find(this.options.slyOldPriceSelector)[isShow ? 'show' : 'hide']();

            if (typeof result != 'undefined' && result.tierPrices && result.tierPrices.length) {
                if (this.options.tierPriceTemplate) {
                    tierPriceHtml = mageTemplate(
                        this.options.tierPriceTemplate,
                        {
                            'tierPrices': result.tierPrices,
                            '$t': $t,
                            'currencyFormat': this.options.jsonConfig.currencyFormat,
                            'priceUtils': priceUtils
                        }
                    );
                    $(this.options.tierPriceBlockSelector).html(tierPriceHtml).show();
                }
            } else {
                $(this.options.tierPriceBlockSelector).hide();
            }

            $(this.options.normalPriceLabelSelector).hide();

            _.each($('.' + this.options.classes.attributeOptionsWrapper), function (attribute) {
                if ($(attribute).find('.' + this.options.classes.optionClass + '.selected').length === 0) {
                    if ($(attribute).find('.' + this.options.classes.selectClass).length > 0) {
                        _.each($(attribute).find('.' + this.options.classes.selectClass), function (dropdown) {
                            if ($(dropdown).val() === '0') {
                                $(this.options.normalPriceLabelSelector).show();
                            }
                        }.bind(this));
                    } else {
                        $(this.options.normalPriceLabelSelector).show();
                    }
                }
            }.bind(this));
        },

        /**
         * Get new prices for selected options
         *
         * @returns {*}
         * @private
         */
        _getNewPrices: function () {
            var $widget = this,
                optionPriceDiff = 0,
                allowedProduct = this._getAllowedProductWithMinPrice(this._CalcProducts()),
                optionPrices = this.options.jsonConfig.optionPrices,
                basePrice = parseFloat(this.options.jsonConfig.prices.basePrice.amount),
                optionFinalPrice,
                newPrices;

            if (!_.isEmpty(allowedProduct)) {
                optionFinalPrice = parseFloat(optionPrices[allowedProduct].finalPrice.amount);
                optionPriceDiff = optionFinalPrice - basePrice;
            }

            if (optionPriceDiff !== 0) {
                newPrices  = this.options.jsonConfig.optionPrices[allowedProduct];
            } else {
                newPrices = $widget.options.jsonConfig.prices;
            }

            return newPrices;
        },

        /**
         * Get prices
         *
         * @param {Object} newPrices
         * @param {Object} displayPrices
         * @returns {*}
         * @private
         */
        _getPrices: function (newPrices, displayPrices) {
            var $widget = this;

            if (_.isEmpty(newPrices)) {
                newPrices = $widget._getNewPrices();
            }
            _.each(displayPrices, function (price, code) {

                if (newPrices[code]) {
                    displayPrices[code].amount = newPrices[code].amount - displayPrices[code].amount;
                }
            });

            return displayPrices;
        },

        /**
         * Get product with minimum price from selected options.
         *
         * @param {Array} allowedProducts
         * @returns {String}
         * @private
         */
        _getAllowedProductWithMinPrice: function (allowedProducts) {
            var optionPrices = this.options.jsonConfig.optionPrices,
                product = {},
                optionFinalPrice, optionMinPrice;

            _.each(allowedProducts, function (allowedProduct) {
                optionFinalPrice = parseFloat(optionPrices[allowedProduct].finalPrice.amount);

                if (_.isEmpty(product) || optionFinalPrice < optionMinPrice) {
                    optionMinPrice = optionFinalPrice;
                    product = allowedProduct;
                }
            }, this);

            return product;
        },

        /**
         * Gets all product media and change current to the needed one
         *
         * @private
         */
        _LoadProductMedia: function () {
            var $widget = this,
                $this = $widget.element,
                productData = this._determineProductData(),
                mediaCallData,
                mediaCacheKey,

                /**
                 * Processes product media data
                 *
                 * @param {Object} data
                 * @returns void
                 */
                mediaSuccessCallback = function (data) {
                    if (!(mediaCacheKey in $widget.options.mediaCache)) {
                        $widget.options.mediaCache[mediaCacheKey] = data;
                    }
                    $widget._ProductMediaCallback($this, data, productData.isInProductView);
                    setTimeout(function () {
                        $widget._DisableProductMediaLoader($this);
                    }, 300);
                };

            if (!$widget.options.mediaCallback) {
                return;
            }

            mediaCallData = {
                'product_id': this.getProduct()
            };

            mediaCacheKey = JSON.stringify(mediaCallData);

            if (mediaCacheKey in $widget.options.mediaCache) {
                $widget._XhrKiller();
                $widget._EnableProductMediaLoader($this);
                mediaSuccessCallback($widget.options.mediaCache[mediaCacheKey]);
            } else {
                mediaCallData.isAjax = true;
                $widget._XhrKiller();
                $widget._EnableProductMediaLoader($this);
                $widget.xhr = $.ajax({
                    url: $widget.options.mediaCallback,
                    cache: true,
                    type: 'GET',
                    dataType: 'json',
                    data: mediaCallData,
                    success: mediaSuccessCallback
                }).done(function () {
                    $widget._XhrKiller();
                });
            }
        },

        /**
         * Enable loader
         *
         * @param {Object} $this
         * @private
         */
        _EnableProductMediaLoader: function ($this) {
            var $widget = this;

            if ($('body.catalog-product-view').length > 0) {
                $this.parents('.column.main').find('.photo.image')
                    .addClass($widget.options.classes.loader);
            } else {
                //Category View
                $this.parents('.product-item-info').find('.product-image-photo')
                    .addClass($widget.options.classes.loader);
            }
        },

        /**
         * Disable loader
         *
         * @param {Object} $this
         * @private
         */
        _DisableProductMediaLoader: function ($this) {
            var $widget = this;

            if ($('body.catalog-product-view').length > 0) {
                $this.parents('.column.main').find('.photo.image')
                    .removeClass($widget.options.classes.loader);
            } else {
                //Category View
                $this.parents('.product-item-info').find('.product-image-photo')
                    .removeClass($widget.options.classes.loader);
            }
        },

        /**
         * Callback for product media
         *
         * @param {Object} $this
         * @param {String} response
         * @param {Boolean} isInProductView
         * @private
         */
        _ProductMediaCallback: function ($this, response, isInProductView) {
            var $main = isInProductView ? $this.parents('.column.main') : $this.parents('.product-item-info'),
                $widget = this,
                images = [],

                /**
                 * Check whether object supported or not
                 *
                 * @param {Object} e
                 * @returns {*|Boolean}
                 */
                support = function (e) {
                    return e.hasOwnProperty('large') && e.hasOwnProperty('medium') && e.hasOwnProperty('small');
                };

            if (_.size($widget) < 1 || !support(response)) {
                this.updateBaseImage(this.options.mediaGalleryInitial, $main, isInProductView);

                return;
            }

            images.push({
                full: response.large,
                img: response.medium,
                thumb: response.small,
                isMain: true
            });

            if (response.hasOwnProperty('gallery')) {
                $.each(response.gallery, function () {
                    if (!support(this) || response.large === this.large) {
                        return;
                    }
                    images.push({
                        full: this.large,
                        img: this.medium,
                        thumb: this.small
                    });
                });
            }

            this.updateBaseImage(images, $main, isInProductView);
        },

        /**
         * Check if images to update are initial and set their type
         * @param {Array} images
         */
        _setImageType: function (images) {
            var initial = this.options.mediaGalleryInitial[0].img;

            if (images[0].img === initial) {
                images = $.extend(true, [], this.options.mediaGalleryInitial);
            } else {
                images.map(function (img) {
                    if (!img.type) {
                        img.type = 'image';
                    }
                });
            }

            return images;
        },

        /**
         * Update [gallery-placeholder] or [product-image-photo]
         * @param {Array} images
         * @param {jQuery} context
         * @param {Boolean} isInProductView
         */
        updateBaseImage: function (images, context, isInProductView) {
            var justAnImage = images[0],
                initialImages = this.options.mediaGalleryInitial,
                imagesToUpdate,
                gallery = context.find(this.options.mediaGallerySelector).data('gallery'),
                isInitial;

            if (isInProductView) {
                imagesToUpdate = images.length ? this._setImageType($.extend(true, [], images)) : [];
                isInitial = _.isEqual(imagesToUpdate, initialImages);

                if (this.options.gallerySwitchStrategy === 'prepend' && !isInitial) {
                    imagesToUpdate = imagesToUpdate.concat(initialImages);
                }

                imagesToUpdate = this._setImageIndex(imagesToUpdate);

                if (!_.isUndefined(gallery)) {
                    gallery.updateData(imagesToUpdate);
                } else {
                    context.find(this.options.mediaGallerySelector).on('gallery:loaded', function (loadedGallery) {
                        loadedGallery = context.find(this.options.mediaGallerySelector).data('gallery');
                        loadedGallery.updateData(imagesToUpdate);
                    }.bind(this));
                }

                if (isInitial) {
                    $(this.options.mediaGallerySelector).AddFotoramaVideoEvents();
                } else {
                    $(this.options.mediaGallerySelector).AddFotoramaVideoEvents({
                        selectedOption: this.getProduct(),
                        dataMergeStrategy: this.options.gallerySwitchStrategy
                    });
                }

                if (gallery) {
                    gallery.first();
                }
            } else if (justAnImage && justAnImage.img) {
                context.find('.product-image-photo').attr('src', justAnImage.img);
            }
        },

        /**
         * Set correct indexes for image set.
         *
         * @param {Array} images
         * @private
         */
        _setImageIndex: function (images) {
            var length = images.length,
                i;

            for (i = 0; length > i; i++) {
                images[i].i = i + 1;
            }

            return images;
        },

        /**
         * Kill doubled AJAX requests
         *
         * @private
         */
        _XhrKiller: function () {
            var $widget = this;

            if ($widget.xhr !== undefined && $widget.xhr !== null) {
                $widget.xhr.abort();
                $widget.xhr = null;
            }
        },

        /**
         * Emulate mouse click on all swatches that should be selected
         * @param {Object} [selectedAttributes]
         * @private
         */
        _EmulateSelected: function (selectedAttributes) {
            $.each(selectedAttributes, $.proxy(function (attributeCode, optionId) {
                var elem = this.element.find('.' + this.options.classes.attributeClass +
                    '[attribute-code="' + attributeCode + '"] [option-id="' + optionId + '"]'),
                    parentInput = elem.parent();

                if (elem.hasClass('selected')) {
                    return;
                }

                if (parentInput.hasClass(this.options.classes.selectClass)) {
                    parentInput.val(optionId);
                    parentInput.trigger('change');
                } else {
                    elem.trigger('click');
                }
            }, this));
        },

        /**
         * Emulate mouse click or selection change on all swatches that should be selected
         * @param {Object} [selectedAttributes]
         * @private
         */
        _EmulateSelectedByAttributeId: function (selectedAttributes) {
            $.each(selectedAttributes, $.proxy(function (attributeId, optionId) {
                var elem = this.element.find('.' + this.options.classes.attributeClass +
                    '[attribute-id="' + attributeId + '"] [option-id="' + optionId + '"]'),
                    parentInput = elem.parent();

                if (elem.hasClass('selected')) {
                    return;
                }

                if (parentInput.hasClass(this.options.classes.selectClass)) {
                    parentInput.val(optionId);
                    parentInput.trigger('change');
                } else {
                    elem.trigger('click');
                }
            }, this));
        },

        /**
         * Get default options values settings with either URL query parameters
         * @private
         */
        _getSelectedAttributes: function () {
            var hashIndex = window.location.href.indexOf('#'),
                selectedAttributes = {},
                params;

            if (hashIndex !== -1) {
                params = $.parseQuery(window.location.href.substr(hashIndex + 1));

                selectedAttributes = _.invert(_.mapObject(_.invert(params), function (attributeId) {
                    var attribute = this.options.jsonConfig.mappedAttributes[attributeId];

                    return attribute ? attribute.code : attributeId;
                }.bind(this)));
            }

            return selectedAttributes;
        },

        /**
         * Callback which fired after gallery gets initialized.
         *
         * @param {HTMLElement} element - DOM element associated with a gallery.
         */
        _onGalleryLoaded: function (element) {
            var galleryObject = element.data('gallery');

            this.options.mediaGalleryInitial = galleryObject.returnCurrentImages();
        },

        /**
         * Sets mediaCache for cases when jsonConfig contains preSelectedGallery on layered navigation result pages
         *
         * @private
         */
        _setPreSelectedGallery: function () {
            var mediaCallData;

            if (this.options.jsonConfig.preSelectedGallery) {
                mediaCallData = {
                    'product_id': this.getProduct()
                };

                this.options.mediaCache[JSON.stringify(mediaCallData)] = this.options.jsonConfig.preSelectedGallery;
            }
        }
    });

    return $.mage.SwatchRenderer;
});