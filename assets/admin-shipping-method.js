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
                console.log(data.data);
                const shipping_options = data.data;
                const modal = document.getElementById("shipping-options");
                const input = document.createElement('INPUT');
                const label = document.createElement('LABEL');
                const n = 0;
                input.setAttribute("type", "radio");
                input.setAttribute("id", shipping_options[n].type + '-' + shipping_options[n].id );
                input.setAttribute("name", shipping_options[n].name);
                input.setAttribute("value", shipping_options[n].id);
                label.setAttribute("for", shipping_options[n].type + '-' + shipping_options[n].id);
                label.textContent = shipping_options[n].price + ' - ' + shipping_options[n].name;
                modal.appendChild(input);
                modal.appendChild(label);
                //<label for="fname">First name:</label><br>
                //</br><input type="text" id="fname" name="fname" value="John"><br></br>
                //add each shipping option to a form
                //on form submit add the shipping option to the order
                //window.location.reload()

            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                alert(errorThrown);
            }
        });

    });


})( jQuery );