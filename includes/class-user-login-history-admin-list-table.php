<?php

class User_Login_History_Admin_List_Table extends User_Login_History_Abstract_List_Table {
    
    public function export_to_CSV() {
        global $current_user;
        $timezone = get_user_meta($current_user->ID, USER_LOGIN_HISTORY_OPTION_PREFIX . "user_timezone", TRUE);
        $data = $this->get_rows(0); // pass zero to get all the records
        //date string to suffix the file nanme: month - day - year - hour - minute
        $suffix = date('n-j-y_H-i');
        // send response headers to the browser
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment;filename=login_log_' . $suffix . '.csv');

        if (!$data) {
            echo 'No record.';
            exit;
        }

        $fp = fopen('php://output', 'w');
        $i = 0;

        foreach ($data as $row) {
            unset($row['meta_value']);
            //calculate duration before time_last_seen - MANDATORY
            $row['duration'] = $this->column_default($row, 'duration');

            $time_last_seen = $row['time_last_seen'];
            $human_time_diff = human_time_diff(strtotime($time_last_seen));
            $time_last_seen = User_Login_History_Date_Time_Helper::convert_timezone($time_last_seen, '', $timezone);
            $row['time_last_seen'] = $human_time_diff . " " . __('ago', 'user-login-history') . " ($time_last_seen)";

            $row['user_id'] = $this->column_default($row, 'user_id');
            $row['current_role'] = $this->column_default($row, 'role');
            $row['old_role'] = $this->column_default($row, 'old_role');
            $row['time_login'] = $this->column_default($row, 'time_login');
            $row['time_logout'] = $this->column_default($row, 'time_logout');

            $row['login_status'] = $this->column_default($row, 'login_status');

            if (is_multisite()) {
                $row['is_super_admin'] = $this->column_default($row, 'is_super_admin');
            } else {
                unset($row['is_super_admin']);
            }
            //output header row
            if (0 == $i) {
                fputcsv($fp, array_keys($row));
            }

            fputcsv($fp, $row);
            $i++;
        }
        fclose($fp);
        die();
    }
}