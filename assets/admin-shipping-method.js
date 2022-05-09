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
                if (data) {
                    const shipping = data.shipping;
                    const label = shipping[0].label;
                    const cost = shipping[0].cost;
                    alert(`Shipping option is ${label} at a cost of ${cost}`);
                }
                // show the control message
                console.log(data);

            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                alert(errorThrown);
            }
        });

    });


})( jQuery );