jQuery(function () {
//date-picker
    jQuery(".datepicker").datepicker({dateFormat: 'yy-mm-dd'});
    jQuery("#date_to").datepicker({dateFormat: 'yy-mm-dd'});
    jQuery("#date_from").datepicker({dateFormat: 'yy-mm-dd'}).bind("change", function () {
        var minValue = jQuery(this).val();
        minValue = jQuery.datepicker.parseDate("yy-mm-dd", minValue);
        minValue.setDate(minValue.getDate() + 0);
        jQuery("#date_to").datepicker("option", "minDate", minValue);
    });
//ajax-timezone
    jQuery("#select_timezone").change(function () {
        jQuery.ajax({
            url: custom_object.ajax_url,
            method: 'post',
            data: {timezone: jQuery(this).val(), action: custom_object.plugin_name+'update_user_timezone'},
            success: function () {
                window.location.reload();
            }
        });
    });
});