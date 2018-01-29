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
});