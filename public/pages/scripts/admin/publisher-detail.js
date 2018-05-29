
/*
    This javascript file used in publisher detail page
    URL: /admin/publishers/1
 */
jQuery(document).ready(function(){

    jQuery('#anc_edit_publisher').on('click',function(){
        jQuery('.display_value_section').hide();
        jQuery('.display_control_section').show();
    })

    jQuery('#btnCancel').on('click',function(){
        jQuery('.display_value_section').show();
        jQuery('.display_control_section').hide();
    });

    /*
    $('#country_id').change(function(){
        $.get(url_get_states,
            { id: $(this).val() },
            function(data) {
                var subcat = $('#state_id');
                subcat.empty();
                subcat.append("<option value='0'>Select state</option>");
                $.each(data, function(index, element) {
                    subcat.append("<option value='"+ element.id +"'>" + element.name + "</option>");
                });
            }
        );
    });
    */
});

// Edit publisher form validation logic
var PublisherEditFormValidation = function () {

    var handleValidation = function() {
        // for more info visit the official plugin documentation:
        // http://docs.jquery.com/Plugins/Validation

        var form = $('#frm_publisher_edit');
        var error = $('.alert-danger', form);
        var success = $('.alert-success', form);

        form.validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block help-block-error', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            ignore: "", // validate all fields including form hidden input
            rules: {
                advertiser_id: {
                    required: true
                },
                publisher_id: {
                    required: true
                },
                name: {
                    minlength: 2,
                    required: true
                },
                email: {
                    email: true
                }
            },

            messages: { // custom messages for radio buttons and checkboxes
                advertiser_id: {
                    required: "Please provide advertiser."
                },
                publisher_id: {
                    required: "Please provide publisher id."
                },
                name: {
                    required: "Please provide publisher name."
                },
                email: {
                    required: "Please provide valid email."
                }
            },

            errorPlacement: function (error, element) { // render error placement for each input type
                if (element.parent(".input-group").size() > 0) {
                    error.insertAfter(element.parent(".input-group"));
                } else if (element.attr("data-error-container")) {
                    error.appendTo(element.attr("data-error-container"));
                } else if (element.parents('.radio-list').size() > 0) {
                    error.appendTo(element.parents('.radio-list').attr("data-error-container"));
                } else if (element.parents('.radio-inline').size() > 0) {
                    error.appendTo(element.parents('.radio-inline').attr("data-error-container"));
                } else if (element.parents('.checkbox-list').size() > 0) {
                    error.appendTo(element.parents('.checkbox-list').attr("data-error-container"));
                } else if (element.parents('.checkbox-inline').size() > 0) {
                    error.appendTo(element.parents('.checkbox-inline').attr("data-error-container"));
                } else {
                    error.insertAfter(element); // for other inputs, just perform default behavior
                }
            },

            invalidHandler: function (event, validator) { //display error alert on form submit
                success.hide();
                error.show();
                Metronic.scrollTo(error, -200);
            },

            highlight: function (element) { // hightlight error inputs
                $(element)
                    .closest('.error_class_div').addClass('has-error'); // set error class to the control group
            },

            unhighlight: function (element) { // revert the change done by hightlight
                $(element)
                    .closest('.error_class_div').removeClass('has-error'); // set error class to the control group
            },

            success: function (label) {
                label
                    .closest('.error_class_div').removeClass('has-error'); // set success class to the control group
            },

            submitHandler: function (form) {
                success.show();
                error.hide();
                //form[0].submit(); // submit the form
                form.submit(); // submit the form
            }

        });
        /*
        jQuery.getJSON(url_get_advertisers).done(
            function( data ) {

                data = $.map(data, function(item) {
                    return { id: item.id, text: item.name };
                });


                jQuery('#advertiser_id').change(function() {
                    form.validate().element($(this)); //revalidate the chosen dropdown value and show error or success message for the input
                }).select2({
                    placeholder: 'Type to find advertiser...',
                    allowClear: true,
                    minimumInputLength: 0,
                    //multiple: true,
                    data: data
                });
                //jQuery("#advertiser_id").select2("data", advertiser_id);
                $("#advertiser_id").select2("val", advertiser_id);

            }
        );
        */
    }

    return {
        //main function to initiate the module
        init: function () {
            handleValidation();
        }

    };

}();

jQuery(document).ready(function() {
    PublisherEditFormValidation.init();

    $(".cls_publisher_share").TouchSpin({
        buttondown_class: 'btn blue',
        buttonup_class: 'btn blue',
        min: 0,
        max: 100,
        step: 0.1,
        decimals: 2,
        boostat: 5,
        maxboostedstep: 10,
        postfix: '%'
    });
});