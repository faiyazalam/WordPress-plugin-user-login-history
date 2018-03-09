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
        
        
 
//table sorting
$(".sorting_hover").css("opacity", "0");
$(".sorting_link").hover(function(){
        $(this).children(".sorting_hover").css("opacity", "1").next().css("opacity", "0");
        }, function(){
       $(this).children(".sorting_hover").css("opacity", "0").next().css("opacity", "1");
    });
});
})(jQuery);

