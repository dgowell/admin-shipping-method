(function( $ ) {
    'use strict';

    $('.inside').on('click','#add_shipping_method', function(){

        // get the order_id from the button tag
        var order_id = $(this).data('order_id');

        // send the data via ajax to the sever
        $.ajax({
            type: 'POST',
            url: tapa_shipping_var.ajaxurl,
            dataType: 'json',
            data: {
                action: 'add_order_shipping',
                order_id: order_id,
            },
            success: function (data, textStatus, XMLHttpRequest) {
                debugger;
                console.log("success");
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                alert(errorThrown);
            }
        });

    });


})( jQuery );