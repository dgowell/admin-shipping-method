(function( $ ) {
    'use strict';

    $('.inside').on('click','#add_shipping_method', function(){

        // get the order_id from the button tag
        var order_id = $(this).data('order_id');
        var nonce = $(this).data('nonce');
        // send the data via ajax to the sever
        $.ajax({
            type: 'POST',
            url: tapa_shipping_var.ajaxurl,
            dataType: 'json',
            data: {
                action: 'add_order_shipping',
                order_id: order_id,
                security: nonce,
            },
            success: function (data, textStatus, XMLHttpRequest) {
                window.location.reload();
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                alert(errorThrown);
            }
        });

    });


})( jQuery );