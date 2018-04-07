(function ($) {
    'use strict';

    $(function () {

//date picker
        $(".datepicker").datepicker({dateFormat: 'yy-mm-dd'});
        $("#date_to").datepicker({dateFormat: 'yy-mm-dd'});
        $("#date_from").datepicker({dateFormat: 'yy-mm-dd'}).bind("change", function () {
            var minValue = $(this).val();
            minValue = $.datepicker.parseDate("yy-mm-dd", minValue);
            minValue.setDate(minValue.getDate() + 0);
            $("#date_to").datepicker("option", "minDate", minValue);
        });

//csv
        $("#download_csv_link").click(function () {
            var export_csv = $("#export-csv");
            export_csv.val('csv');
            $("#submit").trigger('click');
            export_csv.val('');
        });
        //delete all records
        $("#doaction, #doaction2, .delete").click(function (e) {
            if (confirm(admin_custom_object.delete_confirm_message))
            {
                return true;
            }
            return false; 
        });
    });

})(jQuery);
