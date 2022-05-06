(function( $ ) {
    'use strict';

    $('.inside').on('click','#add_shipping_method', function(){
        console.log("clicked");

        // send the data via ajax to the sever
        $.ajax({
            type: 'GET',
            url: tapa_shipping_var.ajaxurl,
            dataType: 'json',
            data: {
                action: 'get_shipping_methods',
            },
            success: function (data, textStatus, XMLHttpRequest) {

                // show the control message
                alert(data);

            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                alert(errorThrown);
            }
        });

    });


})( jQuery );