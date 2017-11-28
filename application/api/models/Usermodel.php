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

  function login($username, $password, $vendor_id = '') {
      $this->db->select('*');
      $this->db->from('users');
      $this->db->where('user_name', $username);
      $this->db->or_where('email', $username);
      $this->db->where('password', $password);
      if($vendor_id)
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
                'vehicle_model' => $post['fullTokenNumber'],
                'mobile'        => $post['mobileNumber'],
                'vehicle_size'  => $post['goodsSize'],
                'checkin_time'  => date("Y-m-d H:i:s"),
                'checkin_type'  => (strlen($post['pin']) == 4 ? 1 : 2),
              ); 

      $res = $this->db->insert('checkin_details', $data);
      $vendor_id = $post['venderId'];
      if(strlen($post['pin'] == 4)) {
        /* decrease inventory after check in */
        $column_name = ($post['goodsSize'] == 1 ? 'small_occupied' : 'big_occupied');
        $query = "UPDATE pricing_details set $column_name = ($column_name + 1) where vendor_id = '$vendor_id'";
        $this->db->query($query);
      }

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

  function get_userdata($user_id) {
    $this->db->select('user_name, email, mobile, user_id');
    $this->db->where(array('user_id' => $user_id));
    $query = $this->db->get('users');
    
    return $query->row();

  }

  function get_data_array($table, $where) {
    $rows = $this->db->get_where($table, $where);

    return $rows->result();
  }

  function calculate_bill_amount($row) {
      $checkin_time = $row->checkin_time;
      $vendor_id = $row->vendor_id;
      $vehicle_size = $row->vehicle_size;

      $pricing_row = $this->get_pricing_details($vendor_id);
      if ($vehicle_size == 1) {
        $first_x_hour = $pricing_row->small_first_hours;
        $first_x_hour_rate= $pricing_row->small_first_hr_rate;
        $rate_applied = $pricing_row->small_hourly_rate;
      } else if($vehicle_size == 2) {
        $first_x_hour = $pricing_row->big_first_hours;
        $first_x_hour_rate= $pricing_row->big_first_hr_rate;
        $rate_applied = $pricing_row->big_hourly_rate;
      }
      
      $duration_occupied_in_hr = ceil((time() - strtotime($row->checkin_time)) / 3600);
      
      $price_in_minute = number_format($rate_applied / 60, 2);
      if ($duration_occupied_in_hr > $first_x_hour) {
        $bill_amount = $first_x_hour_rate + (($duration_occupied_in_hr - $first_x_hour) * $rate_applied);
      } else {
        $bill_amount = $first_x_hour_rate;
      }
      $duration_occupied = ceil((time() - strtotime($row->checkin_time)) / 60 );
      if ($duration_occupied >= 60) {
        $duration_occupied = floor($duration_occupied/60)." Hr ".($duration_occupied%60). " Minutes";
      } else {
        $duration_occupied = $duration_occupied." Minutes";
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
      return true;
    } else {
      return false;
    }
  }

  function updateProfilePic($filename, $post) {
      $this->load->model('Backend');
      $data = array(
        'photo' => ($filename ? $filename : 'no-photo'),
        'mobile'=> $post->mobileNumber,
      );

      $where = array('checkin_id' => $post->transactionId);
      $res = $this->Backend->update_data($data, 'checkin_details', $where);

     return true;
      if($res) {
        return true;
      } else {
        return false;
      }
  }

  function send_sms($mobile, $text) 
  {
      if($mobile) {
        $text = urlencode($text);
        $url = "https://mobilnxt.in/api/push?accesskey=VPWEHZOk1bBokhQofLZNbQMMxSRNGF&to=".$mobile."&text=".$text."&from=VACTST";
        $res = json_decode(file_get_contents($url));
        if($res->status == 'success') {
          return true;
        } else {
          return false;
        }
      } else {
        return false;
      }
  }

    function getNearByVendors($userLat, $userLong)
    {
        $distanceType = 'km';
        $multiplyer = 6371 * 1000;
        $maxDistance = 160.934 * 1000; //offers in 100 miles
        $proximity_cond = '(ROUND( ' . $multiplyer . ' * acos( cos( radians( ' . $userLat . ' ) )'
                  . ' * cos( radians( vendor_lat ) )'
                  . ' * cos( radians( vendor_long ) - radians( ' . $userLong . ' ) )'
                  . ' + sin( radians( ' . $userLat . ' ) ) * sin( radians( vendor_lat ) ) ) )) < ' . $maxDistance;
        $sql = "select * from vendors where $proximity_cond";
        $query = $this->db->query($sql);
        
        return $query->result();
    }

    function getPricingDetails($vendorId)
    {
        return $this->db->get_where('pricing_details', array('vendor_id' => $vendorId))->row();
    }

    function getVehiclesListByUser($user_id)
    {
        return $this->db->get_where('user_vehicles', array('user_id' => $user_id))->result();
    }

    function add_vehicle($post) 
    {
        $data = array(
                  'user_id'       => $post['user_id'],
                  'vehicle_number'    => $post['vehicle_number'],
                  'vehicle_type'           => $post['vehicle_type'],
                ); 

        $res = $this->db->insert('user_vehicles', $data);
       
       if($res) {
        return $res;
       } else {
        return 0;
       }
   }
    
    function isUsed($pin) {
      $where = array('pin' => $pin, 'is_checkout' => 0, 'vendor_id' => $vendor_id);
      $resExist = $this->db->get_where('checkin_details', $where)->row();
      
      if($resExist) {
        return true;
      } else {
        return false;
      }
    } 
      
    function customer_booking_save($post) 
    {
      $vendor_id = $post['vendor_id'];
      $pin = mt_rand(100000, 999999);
      while($this->isUsed($pin)) {
        $pin = mt_rand(100000, 999999);
      }
      
      $hours_booked = ceil((strtotime($post['end_time']) - strtotime($post['start_time'])) / (60*60));
      $data = array(
                'vendor_id'     => $post['vendor_id'],
                'user_id'       => $post['user_id'],
                'vehicle_no'    => $post['vehicle_no'],
                'pin'           => $pin,
                'vehicle_size'  => $post['vehicle_size'],
                'start_time'    => $post['start_time'],
                'payment_mode'  => $post['payment_mode'],
                'hours_booked'  => $hours_booked,
              ); 

      $res = $this->db->insert('customer_booking', $data);
      /* decrease inventory after booking */
      $column_name = ($post['vehicle_size'] == 1 ? 'small_occupied' : 'big_occupied');
      $query = "UPDATE pricing_details set $column_name = ($column_name + 1) where vendor_id = '$vendor_id'";
      $this->db->query($query);

     if($res) {
      return $pin;
     } else {
      return 0;
     }
   }

}
