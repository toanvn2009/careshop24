require(['jquery'], function($) {
        $(document).on('click','.checkout-cart-index #custom_btn_checkout',function() {
            $("#myModal").show();
        });
        
        $('.close').click(function(){
            $("#myModal").hide();
        });
        $('.custom_checkout_guest').click(function(){
            $('.checkout-cart-index #custom_btn').trigger('click');
    });
    });


