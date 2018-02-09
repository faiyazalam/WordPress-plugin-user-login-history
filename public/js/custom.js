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
    });
})(jQuery);

