<?php

class User_Login_History_Public_List_Table_Helper {

    public function prepare_where_query() {

        $fields = array(
            'user_id',
            'username',
            'country_name',
            'browser',
            'ip_address',
            'role',
            'date_from',
            'date_to',
        );
        $output = array();
        $sql_query = FALSE;
        $count_query = FALSE;
        $values = array();
        $date_type = FALSE;

        if (isset($_GET['date_type']) && "login" == $_GET['date_type']) {
            $date_type = 'login';
        }
        if (isset($_GET['date_type']) && "logout" == $_GET['date_type']) {
            $date_type = 'logout';
        }



        foreach ($fields as $field) {
            $data_type = "%s";
            $operator_sign = "=";

            if (isset($_GET[$field]) && "" != $_GET[$field]) {
                $getValue = $_GET[$field];
                if ('user_id' == $field) {
                    $data_type = "%d";
                    $field = 'a.user_id';
                }
                if ('username' == $field) {
                    $operator_sign = "LIKE";
                    $getValue = "%" . $getValue . "%";
                    $field = 'a.username';
                }
                if ('role' == $field) {
                    $field = 'meta_value';
                    $operator_sign = "LIKE";
                    $getValue = "%" . $getValue . "%";
                }

                if ($date_type) {
                    $Date_Time_Helper = new User_Login_History_Date_Time_Helper();
                    $default_timezone = $Date_Time_Helper->get_default_timezone();
                    $getValue = $Date_Time_Helper->convert_to_user_timezone($getValue, 'Y-m-d', $default_timezone);
                    
                    if ('date_from' == $field) {
                        $field = 'time_' . $date_type;
                        $operator_sign = ">=";
                        $getValue = $getValue . " 00:00:00";
                    }
                    if ('date_to' == $field) {
                        $field = 'time_' . $date_type;
                        $operator_sign = "<=";

                        $getValue = $getValue . " 23:59:59";
                    }
                }



                $sql_query .= " AND $field $operator_sign $data_type ";
                $count_query .= " AND $field $operator_sign $data_type ";

                $values[] = $getValue;
            }
        }


        return !$sql_query ? FALSE : array('sql_query' => $sql_query, 'count_query' => $count_query, 'values' => $values);
    }

}
