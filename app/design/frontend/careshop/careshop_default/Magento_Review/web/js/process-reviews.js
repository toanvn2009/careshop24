define([
    'jquery',
	'mage/url'
], function ($,url) {
    'use strict';

    /**
     * @param {String} url
     * @param {*} fromPages
     */
    function processReviews(id, filter, fromPages) {
        var _url = url.build("review/product/listAjax/id/"+id+"");
        if (filter) {
            _url = url.build("review/product/listAjax/id/"+id+"?filter="+filter+"")
        }
        $.ajax({
            url: _url,
            cache: true,
            dataType: 'html',
            showLoader: false,
            loaderContext: $('.product.data.items')
        }).done(function (data) {
            $('.block-review-list ').html(data).trigger('contentUpdated');
            $('[data-role="product-review"] .block-review-list .pages a').each(function (index, element) {
                $(element).click(function (event) { //eslint-disable-line max-nested-callbacks
                    processReviews($(element).attr('href'), true);
                    event.preventDefault();
                });
            });
        }).complete(function () {
			var data = $('.block-review-improvements .nav-block a.active').data('list');
            if (fromPages == true && data=='block-review-list') { //eslint-disable-line eqeqeq
                $('html, body').animate({
                    scrollTop: $('#reviews').offset().top - 50
                }, 300);
            }
        });
    }
	
    return function (config) {
        var reviewTab = $(config.reviewsTabSelector),
            requiredReviewTabRole = 'tab';

        if (reviewTab.attr('role') === requiredReviewTabRole && reviewTab.hasClass('active')) {
            processReviews(config.productId, '', location.hash === '#reviews');
        } else {
            reviewTab.one('beforeOpen', function () {
                processReviews(config.productId);
            });
        }
		
        $(function () {
            $('.product-info-main .reviews-actions a').click(function (event) {
                var anchor, addReviewBlock;

                event.preventDefault();
                anchor = $(this).attr('href').replace(/^.*?(#|$)/, '');
                addReviewBlock = $('#' + anchor);

                if (addReviewBlock.length) {
                    $('.product.data.items [data-role="content"]').each(function (index) { //eslint-disable-line
                        if (this.id == 'reviews') { //eslint-disable-line eqeqeq
                            $('.product.data.items').tabs('activate', index);
                        }
                    });
                    $('html, body').animate({
                        scrollTop: addReviewBlock.offset().top - 50
                    }, 300);
                }

            });
            $(document).on('click', '.list-rate-filter .review-ratings', function(){
                var selected = [];
				$('.list-rate-filter input:checked').each(function() {
					selected.push($(this).val());
				});
                var filter = selected.join('_');
                processReviews(config.productId, filter);
            });
        });
		$(function () { 
			$('body').on('change', '.review-list #sorter', function(){
				var val = $(this).val();
				var url = ''+config.productReviewUrl+'?sort='+val+'';
				processReviews(url,true);
			});
			$(document).on('click', '.block-review-improvements .nav-block li a', function(e) {
				var data = $(this).data('list');
				if(!$(this).hasClass('active')){
					$('.block-review-improvements .nav-block li a').removeClass('active');
					$(this).addClass('active');
                    $('#product-review-container .block-list-data').hide();
					$('.'+data+'').show();
				}
				return false;
			}); 
        });
		
    };
});
