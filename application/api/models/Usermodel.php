<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Usermodel extends CI_Model {
   public function __construct()
    {
        parent::__construct();
    }
  function getUserDetails($user_id) {
      $this->db->select('*');
      $this->db->from('users');
      $this->db->where('user_id', $user_id);
      $this->db->or_where('email', $user_id);
      //$this->db->where('password', $password);
      $query = $this->db->get();
      $num = $query->num_rows();
      $res = array();
      if($num > 0){
        $res = $query->result();
      } 
      
      return $res;
  }

  function login($username, $password) {
      $this->db->select('*');
      $this->db->from('users');
      $this->db->where('user_name', $username);
      $this->db->or_where('email', $username);
      $this->db->where('password', $password);
      $query = $this->db->get();
      $num = $query->num_rows();
      $res = array();
      if($num > 0){
        $res = $query->row();
      } 

      return $res;
  }
}