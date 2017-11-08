<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class User extends CI_Model {
  function __construct() {
          parent::__construct();
        }
  function login($user_name,$password){
      $this->db->select('*');
      $this->db->from('users');
      $this->db->where('user_name', $user_name);
      $this->db->or_where('email', $user_name);
      $this->db->where('password', $password);
      $query = $this->db->get();
      $num = $query->num_rows();
      $res = array();
      if($num > 0){
        $user_data = $query->result();
        $res['user_data'] = $user_data[0];
        $res['status'] = 1;
        
      } else {
        $res['user_data'] = '';
        $res['status'] = 0;
      }
      
      return $res;
  }

  function get_data($table, $where) {
    $rows = $this->db->get_where($table, $where);

    return $rows->row();
  }

}