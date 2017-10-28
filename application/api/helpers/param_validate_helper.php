<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
if (!function_exists('checkids')) {

    function checkids($table, $column_name, $id) {

        $ci = & get_instance();
        $q = $ci->db->get_where($table, array($column_name => $id));
        if ($q->num_rows() > 0) {
            return 1;
        } else {
            return 0;
        }
    }

}
if (!function_exists('checkparams')) {

    function checkparams($postdata, $params) {
        if (count($postdata) == count($params)) {
            $check = 0;
            foreach ($params as $value) {
                if (array_key_exists($value, $postdata)) {
                    $check = 1;
                } else {
                    return 0;
                }
            }
            if ($check) {
                return 1;
            }
        } else {
            return 0;
        }
    }

    function checkselectedparams($postdata, $params) {
        if (count($postdata) != 0 && count($params) != 0) {
            $check = 0;
            foreach ($params as $value) {
                if (array_key_exists($value, $postdata)) { // Checks if the given key or index exists in the array
                    $check = 1;
                } else {
                    return 0;
                }
            }
            if ($check) {
                return 1;
            }
        } else {
            return 0;
        }
    }

}
?>