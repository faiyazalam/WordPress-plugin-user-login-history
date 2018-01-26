<?php
class User_Login_History_Security_Helper {

    static public function verify_nonce($nonce = '_wpnonce', $action = ''){
         if (!wp_verify_nonce(!empty($_REQUEST[$nonce])?$_REQUEST[$nonce]:"", USER_LOGIN_HISTORY_OPTION_PREFIX.$action)) {
                wp_die('invalid nonce');
            }
    }

    

    
    
}
