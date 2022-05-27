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
                action: 'get_shipping_choices',
                order_id: order_id,
                security: nonce,
            },
            success: function (data, textStatus, XMLHttpRequest) {

                const modal = document.getElementById("shipping-options");

                if (Object.keys(data.data).length === 0) {
                    //we don't want to repeat the message if it's there
                    var no_shipping_text = document.getElementById("no-shipping-text");
                    if (!no_shipping_text) {
                        const text = document.createElement("P");
                        text.textContent = "There are no shipping options, check that the order has products and a shipping address!";
                        text.setAttribute("id", "no-shipping-text");
                        modal.appendChild(text);
                    }
                } else {
                    //we don't want more than two forms
                    const element = document.getElementById("shipping-options-form");
                    const shipping_options = data.data;
                    if (typeof(element) != 'undefined' && element != null) {
                        // Do nothing
                    } else {
                        //create form
                        const form = document.createElement("FORM");
                        modal.appendChild(form);
                        form.name='shipping-options-form';
                        form.id = "shipping-options-form";

                        shipping_options.forEach(option => {
                            const div = document.createElement("DIV");
                            const input = document.createElement('INPUT');
                            const label = document.createElement('LABEL');
                            input.setAttribute("type", "radio");
                            input.setAttribute("id", option.type + '-' + option.id );
                            input.setAttribute("name", option.type);
                            input.setAttribute("value", option.id);
                            input.setAttribute("data-price", option.price);
                        input.setAttribute("data-display_name", option.name); 
                            label.setAttribute("for", option.type + '-' + option.id);
                            label.textContent = option.price + ' - ' + option.name;
                            div.appendChild(input);
                            div.appendChild(label);
                            form.appendChild(div);
                        });

                        const submit = document.createElement("INPUT");
                        submit.setAttribute("type", "submit");
                        submit.setAttribute("value", "submit");
                        submit.setAttribute("id", "submit-shipping");
                        submit.setAttribute("style", "margin-top:15px;")
                        submit.setAttribute("data-nonce", nonce);
                        submit.setAttribute("data-order_id", order_id);
                        form.appendChild(submit);
                    }

                    //on form submit add the shipping option to the order
                }

            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                alert(errorThrown);
            }
        });

    });

    $('#shipping-options').on('click','#submit-shipping', function(e){
        e.preventDefault();
        var shipping_id, shipping_price, shipping_name;
        //get the order_id from the button tag
        var order_id = $(this).data('order_id');
        var nonce = $(this).data('nonce');
        //send the data via ajax to the sever
        var options  = e.target.form.elements['table_rate'];
        const choice = parseInt(e.target.form.elements['table_rate'].value);
        //convert to array from node list if more than 1
        if (options.length > 1) {
            options = Array.from(options);
            shipping_id = options[choice].id;
            shipping_price = options[choice].dataset.price;
            shipping_name = options[choice].dataset.display_name;
        } else {
            shipping_id = options.id;
            shipping_price = options.dataset.price;
            shipping_name = options.dataset.display_name;
        }

        //work out which shippping option is checked and return the values
        //if input is checked
        //extract data

        $.ajax({
            type: 'POST',
            url: tapa_shipping_var.ajaxurl,
            dataType: 'json',
            data: {
                action: 'add_shipping_choice_to_order',
                security: nonce,
                order_id,
                shipping_id,
                shipping_price,
                shipping_name,
            },
            success: function (data, textStatus, XMLHttpRequest) {
                location.reload();
                return false;
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                alert(errorThrown);
            }
        });
    });


})( jQuery );