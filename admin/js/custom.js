jQuery(function () {
//date picker
    jQuery(".datepicker").datepicker({dateFormat: 'yy-mm-dd'});
    jQuery("#date_to").datepicker({dateFormat: 'yy-mm-dd'});
    jQuery("#date_from").datepicker({dateFormat: 'yy-mm-dd'}).bind("change", function () {
        var minValue = jQuery(this).val();
        minValue = jQuery.datepicker.parseDate("yy-mm-dd", minValue);
        minValue.setDate(minValue.getDate() + 0);
        jQuery("#date_to").datepicker("option", "minDate", minValue);
    });

//csv
    jQuery("#download_csv_link").click(function () {
        var export_csv = jQuery("#export-csv");
        export_csv.val('csv');
                jQuery("#submit").trigger('click');
        export_csv.val('');
    });
    //delete all records
    jQuery("#doaction, .delete").click(function (e) {
        if (confirm(admin_custom_object.delete_confirm_message))
        {
            return true;
        }
        return false;
    });
});