(function ($) {
    'use strict';

    $(function () {
//date picker
        $("#date_to").datepicker({dateFormat: 'yy-mm-dd'});
        $("#date_from").datepicker({dateFormat: 'yy-mm-dd'}).bind("change", function () {
            var minValue = $(this).val();
            minValue = $.datepicker.parseDate("yy-mm-dd", minValue);
            minValue.setDate(minValue.getDate() + 0);
            $("#date_to").datepicker("option", "minDate", minValue);
        });
        
        //validate date range
        $("#submit").click(function(){
           var date_to = $("#date_to").val();
           var date_from = $("#date_from").val();
           
           if((date_to && date_from) || (!date_to && !date_from))
           {
               return true;
           }
           
           alert(admin_custom_object.invalid_date_range_message);
           return false;
        });

//csv
        $("#download_csv_link").click(function () {
            let csv = $("#csv");
            csv.val('1');
            $("#submit").trigger('click');
            csv.val('');
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
    
  
    $("#show_hide_advanced_search").click(function(){
        let  show_hide_advanced_search =  $(this);
        let advanced_search = $("#advanced_search"); 
        
        if("none" == advanced_search.css("display"))
        {
           advanced_search.show("fast", function(){
               show_hide_advanced_search.text(admin_custom_object.hide_advanced_filters);
           });
           
        }else{
             advanced_search.hide("fast", function(){
                show_hide_advanced_search.text(admin_custom_object.show_advanced_filters);
           });
        }
        return false;
    });

})(jQuery);
