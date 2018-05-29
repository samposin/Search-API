

jQuery(document).ready(function(){

	jQuery('#datatable_ajax').on('change', 'select', function() {

		Metronic.startPageLoading();
		var api_priority=jQuery(this).data('api-priority');
		var api_name=jQuery(this).val();
		var geo=jQuery(this).data('geo')

		console.log(api_priority);
		console.log(api_name);
		console.log(geo);


		jQuery.ajax({
			type: "POST",
			url: advertiser_search_defaults_submit_url,
			dataType:'json',
			data: "geo="+geo+"&api_priority="+api_priority+"&api_name="+api_name,
			headers: { 'X-CSRF-TOKEN' : csrf_token },
			success:function(json){
				Metronic.stopPageLoading();
			},
			error: function( xhr, tStatus, err ) {
                console.log('error');
                Metronic.stopPageLoading();
            }
		});
	});
    //AdvertiserListDataTableAjax.init();
    jQuery('#anc_create_advertiser').on('click',function(){
        jQuery("#modal_create_advertiser").modal('show');
    });


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
});

// Advertiser list through datatable logic
var AdvertiserListDataTableAjax = function () {

    var initPickers = function () {
        //init date pickers
        $('.date-picker').datepicker({
            rtl: Metronic.isRTL(),
            autoclose: true
        });
    }

    var handleRecords = function () {

        var grid = new Datatable();
        var grid_ele=$("#datatable_ajax");

        grid.init({
            src: grid_ele,
            onSuccess: function (grid) {
                // execute some code after table records loaded
            },
            onError: function (grid) {
                // execute some code on network or other general error  
            },
            onDataLoad: function(grid) {
                // execute some code on ajax data load
            },
            loadingMessage: 'Loading...',
            dataTable: { // here you can define a typical datatable settings from http://datatables.net/usage/options 

                // Uncomment below line("dom" parameter) to fix the dropdown overflow issue in the datatable cells. The default datatable layout
                // setup uses scrollable div(table-scrollable) with overflow:auto to enable vertical scroll(see: assets/global/scripts/datatable.js). 
                // So when dropdowns used the scrollable div should be removed. 
                //"dom": "<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'<'table-group-actions pull-right'>>r>t<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'>>",
                
                "bStateSave": true, // save datatable state(pagination, sort, etc) in cookie.
                "dom": "<'row'<'col-xs-3 col-sm-3 col-md-3 col-lg-3'><'col-xs-9 col-sm-9 col-md-9 col-lg-9 text-right'pli><'col-md-4 col-sm-12'<'table-group-actions pull-right'>>r><'table-scrollable't><'row' <'col-xs-3 col-sm-3 col-md-3 col-lg-3 anc_add_contacts_cont clear'> <'col-xs-9 col-sm-9 col-md-9 col-lg-9 text-right'pli><'col-md-4 col-sm-12'>>", // datatable layout
                "lengthMenu": [
                    [10, 20, 50, 100, 150, -1],
                    [10, 20, 50, 100, 150, "All"] // change per page values here
                ],
                "pageLength": 10, // default record count per page
                "ajax": {
                    "url": "advertisers-list-ajax", // ajax source
                    "method":"post",
                     headers: { 'X-CSRF-TOKEN' : csrf_token },
                },
                "order": [
                    [1, "asc"]
                ],// set first column as a default sort by asc
                "columnDefs": [ {
                    "targets"  : 'no-sort',
                    "orderable": false,
                }]
            }
        });

        // handle group actionsubmit button click
        grid.getTableWrapper().on('click', '.table-group-action-submit', function (e) {
            e.preventDefault();
            var action = $(".table-group-action-input", grid.getTableWrapper());
            if (action.val() != "" && grid.getSelectedRowsCount() > 0) {
                grid.setAjaxParam("customActionType", "group_action");
                grid.setAjaxParam("customActionName", action.val());
                grid.setAjaxParam("id", grid.getSelectedRows());
                grid.getDataTable().ajax.reload();
                grid.clearAjaxParams();
            } else if (action.val() == "") {
                Metronic.alert({
                    type: 'danger',
                    icon: 'warning',
                    message: 'Please select an action',
                    container: grid.getTableWrapper(),
                    place: 'prepend'
                });
            } else if (grid.getSelectedRowsCount() === 0) {
                Metronic.alert({
                    type: 'danger',
                    icon: 'warning',
                    message: 'No record selected',
                    container: grid.getTableWrapper(),
                    place: 'prepend'
                });
            }
        });

        // handle filter submit button click
        grid_ele.on('click', '.filter-submit', function(e) {
            // get all typeable inputs
            $('textarea.form-filter, select.form-filter, input.form-filter:not([type="radio"],[type="checkbox"])', grid_ele).each(function() {
                jQuery('[name="hdn_'+$(this).attr("name")+'"]').val($(this).val());
            });

            // get all checkboxes
            $('input.form-filter[type="checkbox"]:checked', grid_ele).each(function() {
                jQuery('[name="hdn_'+$(this).attr("name")+'"]').val($(this).val());
            });

            // get all radio buttons
            $('input.form-filter[type="radio"]:checked', grid_ele).each(function() {
                jQuery('[name="hdn_'+$(this).attr("name")+'"]').val($(this).val());
            });
        });

        console.log(grid_ele);

        // handle export drop down item click
        /*
        jQuery('#companies_export_dropdown').on('click', 'li a', function (e) {
            if(jQuery(this).data('export-type')=='csv') {
                console.log(jQuery(this).data('export-type'));

                var frm_datatable=grid_ele.closest('form');

                frm_datatable.prop('action','/companies/export/csv');
                jQuery('#action',frm_datatable).val('export_csv');
                frm_datatable.submit();

            }
        })
        */
    }

    return {

        //main function to initiate the module
        init: function () {

            initPickers();
            handleRecords();
        }

    };

}();

// Add advertiser form validation logic
var AdvertiserAddFormValidation = function () {

    var handleValidation = function() {
        // for more info visit the official plugin documentation:
        // http://docs.jquery.com/Plugins/Validation

        var form = $('#frm_advertiser_add');
        var error = $('.alert-danger', form);
        var success = $('.alert-success', form);

        form.validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block help-block-error', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            ignore: "", // validate all fields including form hidden input
            rules: {

                name: {
                    minlength: 2,
                    required: true
                }
            },

            messages: { // custom messages for radio buttons and checkboxes
                name: {
                    required: "Please provide advertiser name."
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
                    .closest('.form-group').addClass('has-error'); // set error class to the control group
            },

            unhighlight: function (element) { // revert the change done by hightlight
                $(element)
                    .closest('.form-group').removeClass('has-error'); // set error class to the control group
            },

            success: function (label) {
                label
                    .closest('.form-group').removeClass('has-error'); // set success class to the control group
            },

            submitHandler: function (form) {
                success.show();
                error.hide();
                form[0].submit(); // submit the form
            }

        });

        jQuery.getJSON(url_get_advertiser_widgets).done(
            function( data ) {

                data = $.map(data, function(item) {
                    return { id: item.id, text: item.name };
                });

                jQuery('#advertiser_widget_ids').change(function() {
                    form.validate().element($(this)); //revalidate the chosen dropdown value and show error or success message for the input
                }).select2({
                    placeholder: 'Type to find widgets...',
                    allowClear: true,
                    minimumInputLength: 0,
                    multiple: true,
                    data: data
                });

            }
        );

        jQuery('#advertiser_type_id').change(function() {
            form.validate().element($(this)); //revalidate the chosen dropdown value and show error or success message for the input
        }).select2({
            placeholder: 'Type to find advertiser...',
            allowClear: true,
            minimumInputLength: 0 //,
            //multiple: true,
            //data: data
        });


    }

    return {
        //main function to initiate the module
        init: function () {
            handleValidation();
        }

    };

}();

jQuery(document).ready(function() {
    AdvertiserAddFormValidation.init();
});