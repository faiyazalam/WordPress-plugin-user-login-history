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
    jQuery("#download_csv_button").click(function () {
        var export_user_login_history = jQuery("#export-user-login-history");
        export_user_login_history.val('csv');
                jQuery("#submit").trigger('click');
        export_user_login_history.val('');
    });
    jQuery("#delete_all_user_login_history").click(function (e) {
        e.preventDefault();
        if (confirm(ulh_admin_custom_object.delete_confirm_message))
        {
            window.location.href = ulh_admin_custom_object.admin_url+'admin.php?page=user-login-history&delete_all_user_login_history=1';
        }
        return false;
    });
    jQuery(".userloginhistories").attr("border", "");
});