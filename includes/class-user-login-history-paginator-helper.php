<?php

class User_Login_History_Paginator_Helper {

    public function pagination($options = array()) {
        global $wpdb;
        $pagenum = isset($_GET['pagenum']) ? absint($_GET['pagenum']) : 1;
        $limit = isset($options['limit']) ? $options['limit'] : 10;
        $values = isset($options['values']) ? $options['values'] : NULL;
        $sql_query = $options['sql_query'];
        $count_query = $options['count_query'];
        $offset = ( $pagenum - 1 ) * $limit;

        $total = $wpdb->get_var($wpdb->prepare($count_query, $values));

        $num_of_pages = ceil($total / $limit);
        $sql_query = $sql_query . " LIMIT  $offset, $limit";

        $rows = $wpdb->get_results($wpdb->prepare($sql_query, $values));

        $page_links = paginate_links(array(
            'base' => add_query_arg('pagenum', '%#%'),
            'format' => '',
            'prev_text' => __('&laquo;', 'user-login-history'),
            'next_text' => __('&raquo;', 'user-login-history'),
            'total' => $num_of_pages,
            'current' => $pagenum
        ));
        return array('page_links' => $page_links, 'rows' => $rows);
    }

}
