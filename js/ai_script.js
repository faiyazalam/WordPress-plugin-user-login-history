function validate_name(){
    var check = document.getElementById('ai_visible_name');
    if (check.checked){
        document.getElementById('ai_enable_require_name').disabled ="";
    }else{
		document.getElementById('ai_enable_require_name').checked ="";
        document.getElementById('ai_enable_require_name').disabled ="disabled";
    }
}
function validate_phone(){
    var check = document.getElementById('ai_visible_phone');
    if (check.checked){
        document.getElementById('ai_enable_require_phone').disabled ="";
    }else{
		document.getElementById('ai_enable_require_phone').checked ="";
        document.getElementById('ai_enable_require_phone').disabled ="disabled";
    }
}
function validate_website(){
    var check = document.getElementById('ai_visible_website');
    if (check.checked){
        document.getElementById('ai_enable_require_website').disabled ="";
    }else{
		document.getElementById('ai_enable_require_website').checked ="";
        document.getElementById('ai_enable_require_website').disabled ="disabled";
    }
}
function validate_subject(){
    var check = document.getElementById('ai_visible_subject');
    if (check.checked){
        document.getElementById('ai_enable_require_subject').disabled ="";
    }else{
		document.getElementById('ai_enable_require_subject').checked ="";
        document.getElementById('ai_enable_require_subject').disabled ="disabled";
    }
}
function validate_comment(){
    var check = document.getElementById('ai_visible_comment');
    if (check.checked){
        document.getElementById('ai_enable_require_comment').disabled ="";
    }else{
		document.getElementById('ai_enable_require_comment').checked ="";
        document.getElementById('ai_enable_require_comment').disabled ="disabled";
    }
}