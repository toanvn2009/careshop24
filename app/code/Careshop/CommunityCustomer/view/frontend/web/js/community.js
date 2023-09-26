require(["jquery", "mage/url", "mage/validation"],function($, urlBuilder) {
    $(document).ready(function() {
        var dataForm = $('#community-customer-form');
            dataForm.mage('validation', {});
        $('#community-custom-btn').click(function(){
            var status = dataForm.validation('isValid');
            if(status == false) return; 
            console.log(status);
            var url = urlBuilder.build('community/customer/save/');
            var action = $(this).data('action');
            var formdata = $('#community-customer-form').serializeArray().reduce(function(obj, item) {
                obj[item.name] = item.value;
                return obj;
            }, {});
            $.ajax({
                url: url,
                type: 'POST',
                dataType: 'json',
                showLoader: true, 
                data: {
                    formdata: formdata, action: action,
                },
                success: function(response) {
                   $('.msg').html(response.message);
                },
                error: function (xhr, status, errorThrown) {
                    console.log('Error happens. Try again pub.');
                }
            });
        });

        $('#community-reset-btn').click(function(){
            $(':input','#community-customer-form')
            .not(':button, :submit, :reset, :hidden')
            .val('')
            .removeAttr('checked')
            .removeAttr('selected');

            $('html, body').animate({
                scrollTop: $("#community-customer-form").offset().top
            }, 1000);

        })


        $("#but_upload").click(function(){
            var fd = new FormData();
            var files = $('#file')[0].files;
            var url = urlBuilder.build('community/customer/save/');
            // Check file selected or not
            if(files.length > 0 ){
            fd.append('file',files[0]);
                $.ajax({
                    url: url,
                    type: 'post',
                    data: fd,
                    contentType: false,
                    processData: false,
                    success: function(response){
                        console.log(response);
                        if(response != 0){
                            $("#img").attr("src",response['avatar_url']); 
                            $(".preview img").show(); // Display image element
                        }
                    },
                });
            }else{
                alert("Please select a file.");
            }
        });

        $("#list_avatar_default img, #tab-avatar-albums img").click(function(){
            var img = $(this).attr('src');
            var avatar = $(this).data('avatar');
            var url = urlBuilder.build('community/customer/save/');
            $.ajax({
                url: url,
                type: 'POST',
                dataType: 'json',
                showLoader: true, 
                data: {
                    img: img,
                    avatar:avatar,
                    type:'update_avatar'
                  
                },
                success: function(response) {
                    if(response != 0){
                        $("#img").attr("src",response['avatar_url']); 
                        $(".preview img").show(); 
                    }
                },
                error: function (xhr, status, errorThrown) {
                }
            });
        })
        
        $('select[name="bookmarks-opt"]').change(function() {
            var selected = $(this).val();
           if(selected == 0) {
                var checkBoxValues = [];
                $.each($("input[name='bookmark']:checked"), function(){
                    checkBoxValues.push($(this).val());
                    $(this).parent().parent().remove();
                });
           } else if (selected == 1) {
                $('.cb-element').each(function(){
                    $(this).prop('checked', true);
                })
           } else if (selected == 2) {
                $('.cb-element').each(function(){
                    $(this).prop('checked', false);
                })
           }
           return false; 
        });


        $('select[name="my-sub-opt"]').change(function() {
            var selected = $(this).val();
           if(selected == 0) {
                var checkBoxValues = [];
                $.each($("#tab-my-sub input[name='subscription']:checked"), function(){
                    checkBoxValues.push($(this).val());
                    $(this).parent().parent().remove();
                });
           } else if (selected == 1) {
                $('.cb-element2').each(function(){
                    $(this).prop('checked', true);
                })
           } else if (selected == 2) {
                $('.cb-element2').each(function(){
                    $(this).prop('checked', false);
                })
           }
           return false; 
        });

    });
});