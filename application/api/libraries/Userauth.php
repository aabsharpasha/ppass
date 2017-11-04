<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Userauth {
         var $CI;
        function __construct() {
          $this->CI =& get_instance();
          $this->CI->load->helper('url');
          $this->CI->config->item('base_url');
          $this->CI->load->library('session');
          $this->CI->load->model('usermodel');
        }

        public function authentication($role=NULL)
        {     
          $active_user = $this->CI->session->userdata('active_user');          
          if(empty($active_user)){
            redirect(base_url());
          } elseif(($active_user->user_type != 'admin') && ($active_user->user_type != $role)){
            redirect(base_url());
          }
        }

        public function is_exist_data($table, $where) {
          return $this->CI->usermodel->is_exist_data($table, $where);
        }
}