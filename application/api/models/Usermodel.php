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
      //$this->db->or_where('email', $username);
      $this->db->where('password', $password);
      $this->db->where('vendor_id', $vendor_id);
      $query = $this->db->get();
      $num = $query->num_rows();
      //echo  $this->db->last_query(); exit;
      $res = array();
      if($num > 0){
        $res = $query->row();
      } else {
        return false;
      }

      return $res;
  }

  function checkin_vendor_insert($post) {
    $where = array('is_checkout' => '0', 'pin' => $post['pin']);
    $data = array(
                'vendor_id'     => $post['venderId'],
                'user_id'       => $post['userId'],
                'vehicle_no'    => $post['tokenNumber'],
                'pin'           => $post['pin'],
                'vehicle_model' => $post['tokenNumber1'],
                'mobile'        => $post['mobileNumber'],
                'vehicle_size'  => $post['goodsSize'],
                'checkin_time'  => date("Y-m-d H:i:s"),
                'checkin_type'  => (strlen($post['pin']) == 4 ? 1 : 2),
              ); 

      $res = $this->db->insert('checkin_details', $data);
      $vendor_id = $post['venderId'];
      /* decrease inventory after check in */
      $column_name = ($post['goodsSize'] == 1 ? 'small_occupied' : 'big_occupied');
      $query = "UPDATE pricing_details set $column_name = ($column_name + 1) where vendor_id = '$vendor_id'";
      $this->db->query($query);

    if($res) {
      return $res;
    } else {
      return 0;
    }
  }

  function is_exist_data($table, $where) {
      $rows = $this->db->get_where($table, $where);

      if($rows->num_rows()) {
        return true;
      } else {
        return false;
      }
    
  }

  function get_data($table, $where) {
    $rows = $this->db->get_where($table, $where);

    return $rows->row();
  }

  function calculate_bill_amount($row) {
      $checkin_time = $row->checkin_time;
      $vendor_id = $row->vendor_id;
      $vehicle_size = $row->vehicle_size;

      $pricing_row = $this->get_pricing_details($vendor_id);
      if($vehicle_size == 1) {
        $rate_applied = $pricing_row->small_hourly_rate;
      } else if($vehicle_size == 2) {
        $rate_applied = $pricing_row->big_hourly_rate;
      }
      $duration_occupied = number_format((time() - strtotime($row->checkin_time)) / 60);
      $price_in_minute = number_format($rate_applied / 60, 2);
      $bill_amount = $duration_occupied * $price_in_minute;

      if($duration_occupied >= 60) {
        $duration_occupied = floor($duration_occupied/60)." Hr ".($duration_occupied%60). " Minutes";
      } else {
        $duration_occupied = $duration_occupied." Minutes";
        $bill_amount = $rate_applied;
      }
      

      $return_arr['billAmount'] = number_format($bill_amount);
      $return_arr['durationOccupied'] = $duration_occupied;
      
      $this->load->model('Backend');
      $data['bill_amount'] = number_format($bill_amount);
      $data['duration_occupied'] = $duration_occupied;
      $this->Backend->update_data($data, 'checkin_details', array('checkin_id' => $row->checkin_id));

      return $return_arr;

  }

  function get_pricing_details($vendor_id) {
    return $this->get_data('pricing_details', array('vendor_id' => $vendor_id));
  }

  function checkout($post, $row) {
    $this->load->model('Backend');
    $paid_amount = $post['forceCheckout'] ? $post['receivedAmount'] : $row->bill_amount;
    $data = array(
      'checkin_id'        => $post['transactionId'],
      'bill_amount'       => $row->bill_amount,
      'paid_amount'       => $paid_amount,
      'is_force_checkout' => $post['forceCheckout'],
      'checkout_time'     => date('Y-m-d H:i:s'),
      'payment_mode'      => $post['paymentMode'],
      'duration_occupied' => $row->duration_occupied,
      'user_id_checkout'  => $post['userId'],
    );
    $res = $this->Backend->insert_data($data, 'checkout_details');
    if($res) {
      $where = array('checkin_id' => $post['transactionId']);
      $this->Backend->update_data(array('is_checkout' => '1'), 'checkin_details', $where);
      //echo $this->db->last_query(); exit;
      return true;
    } else {
      return false;
    }
  }

  function updateProfilePic($filename, $post) {
      $this->load->model('Backend');
      $data = array(
        'photo' => $filename,
        'mobile'=> $post->mobileNumber,
      );

      $where = array('checkin_id' => $post->transactionId);
      $res = $this->Backend->update_data($data, 'checkin_details', $where);

     return true;
      if($res) {
        //echo $this->db->last_query(); exit;
        return true;
      } else {
        return false;
      }
  }

  function send_sms($post) {
    $data = $this->get_data('checkin_details', array('checkin_id' => $post['transactionId']));
    if($data) {
      return $data;
    } else {
      return false;
    }
  }

}
