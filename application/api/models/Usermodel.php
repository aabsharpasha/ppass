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

  function login($username, $password, $vendor_id) {
      $this->db->select('*');
      $this->db->from('users');
      $this->db->where('user_name', $username);
      $this->db->or_where('email', $username);
      $this->db->where('password', $password);
      $this->db->where('vendor_id', $vendor_id);
      $query = $this->db->get();
      $num = $query->num_rows();
      $res = array();
      if($num > 0){
        $res = $query->row();
      } 

      return $res;
  }

  function checkin_vendor_insert($post) {
    $where = array('is_checkout' => '0', 'pin' => $post['pin']);
    $rows = $this->db->get_where('checkin_details', $where);

    if($rows->num_rows() == 0) {
    
      $data = array(
                'vendor_id'     => $post['venderId'],
                'user_id'       => $post['userId'],
                'vehicle_no'    => $post['tokenNumber'],
                'pin'           => $post['pin'],
                'vehicle_model' => $post['modelNumber'],
                'mobile'        => $post['mobileNumber'],
                'vehicle_size'  => $post['goodsSize'],
                'checkin_time'  => date("Y-m-d H:i:s"),
                'checkin_type'  => (strlen($post['pin']) == 4 ? 1 : 2),
              ); 

      $res = $this->db->insert('checkin_details', $data);
    }

    if($res) {
      return $res;
    } else {
      return 0;
    }
  }
}