<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
require(APPPATH . '/libraries/REST_Controller.php');

/**
 * Description of login

 */
class Vendor extends REST_Controller {

    function __construct() {

        parent::__construct();

        $this->load->model('usermodel');
    }

    function login_post() 
    {
            try {

                $allowParam = array(
                'vendorId',
                'userName',
                'password'
                );
         
            if (checkselectedparams($this->post(), $allowParam)) {
                $res = $this->usermodel->login($this->post('userName'), md5($this->post('password')), $this->post('vendorId'));

                if (!empty($res)) {
                    $responseCode = 200;
                    $MESSAGE = "Logged in Successfully";
                } else {
                    $responseCode = 303;
                    $MESSAGE = MSG303;
                }
            } else {
                $responseCode = 302;
                $MESSAGE = MSG302;
            }

            
            $resp = array( 
                        'responseMessage' => $MESSAGE,
                        'responseCode'    => $responseCode
                     );
             if($res) {
                $resp['userId'] = $res->user_id;
                $resp['vendorId'] = $res->vendor_id;
             } 
           
            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in VendorLogin function - ' . $ex);
        }
    }


    function checkin_vendor_post() 
    {
            try {
                $allowParam = array(
                'userId',
                'venderId',
                'tokenNumber',
                'pin',
                'goodsSize'
                );
          
                if (checkselectedparams($this->post(), $allowParam)) {
                    $vendor_id = $this->post('venderId');
                    $allowed_length = array(4,6);

                    if(!in_array(strlen($this->post('pin')), $allowed_length)) {

                        $MESSAGE = 'Enter 4 digit PIN. For Prebook, enter 6 digit PIN.';
                        $responseCode = 304;
                    } else if(strlen($this->post('tokenNumber')) != 4) {
                        $MESSAGE = 'Vehicle number must be last 4 digit.';
                        $responseCode = 304;
                    } else {
                         $where = array('vendor_id' => $this->post('vendor_id'), 'vehicle_no' => $this->post('tokenNumber'), 'pin' =>  $this->post('pin'));
                         $is_prebook = $this->db->get_where('customer_booking', $where)->row();
                        
                         if((strlen($this->post('pin')) == 6) && !$is_prebook && substr($this->post('pin'),0,1) != 5) {
                                     $MESSAGE = 'Enter 4 digit PIN. For Prebook, enter 6 digit PIN.';
                                    $responseCode = 304;
                         } else if((strlen($this->post('pin')) == 6) && substr($this->post('pin'),0,1) == 5 && strlen($this->post('tokenNumber')) == 4 && empty($this->post('fullTokenNumber'))) {

                                     $MESSAGE = 'Please enter full vehilce no as you seems to be pass user.';
                                    $responseCode = 304;
                         } else {
                            $this->load->library('userauth');
                        $vechicle_size = $this->post('goodsSize');
                        if($this->post('fullTokenNumber')) {
                            $where = array('vehicle_model' => $this->post('fullTokenNumber'), 'is_checkout' => '0', 'vehicle_size' => $vechicle_size, 'vendor_id' => $vendor_id);
                            $resExist = $this->userauth->is_exist_data('checkin_details', $where);
                        }  else {
                            $where = array('vehicle_no' => $this->post('tokenNumber'), 'is_checkout' => '0', 'vehicle_size' => $vechicle_size, 'vendor_id' => $vendor_id);
                            $resExist = $this->userauth->is_exist_data('checkin_details', $where);
                        }
                      //  echo $resExist; exit;
                         if(!$resExist) {
                            $res = $this->usermodel->checkin_vendor_insert($this->post());
                            if ($res) {
                                $mobile = $this->post('mobileNumber');
                                if($mobile) {
                                    $vendor = $this->get_vendor_details($vendor_id);
                                    if($this->post('tokenNumber'))
                                        $vehicle_no = $this->post('tokenNumber');
                                    else
                                        $vehicle_no = $this->post('fullTokenNumber');

                                    $text = "Dear User, You parked ur vehicle at the ".$vendor->vendor_address." at ".date('d-m-Y, h:i A').". For easy check-out from parking, pls use ur PIN ".$this->post('pin')." at exit.";
                                    //if pass no check in msg
                                    $pin = $this->post('pin');
                                    if (!((strlen(trim($pin)) == 6) && (substr($pin, 0, 1) == 5))) {
                                        $this->usermodel->send_sms($mobile, $text);
                                    }
                                }
                                $MESSAGE = "Check-in Success";
                                $responseCode = 200;
                             } else {
                                $MESSAGE = MSG304;
                                $responseCode = 304;
                             }
                        } else {
                            $MESSAGE = 'Matching last 4 digits found, please enter full vehicle number.';
                            $responseCode = 304;
                        }
                        }
                        
                    }
                } else {
                    $MESSAGE = MSG302;
                    $responseCode = 302;
                }
               
                $resp = array( 
                            'responseMessage' => $MESSAGE,
                            'responseCode'    => $responseCode
                        );
               
                $this->response($resp, 200);
            } catch (Exception $ex) {
                throw new Exception('Error in VendorLogin function - ' . $ex);
            }
    }

   function gettokendetails_post() {

        try {
                $allowParam = array(
                'venderId',
                'tokenNumber'
                );
                
                if (checkselectedparams($this->post(), $allowParam)) {
                    
                        $vendor_id = $this->post('venderId');
                        $vehicle_no = $this->post('tokenNumber');
                        $where = array('vendor_id' => $vendor_id, 'vehicle_no' => $vehicle_no, 'is_checkout' => '0');
                        $rows = $this->usermodel->get_data_array('checkin_details', $where);
                        //print_r($rows); exit;
                        if($rows) {
                             $MESSAGE = "Details populated successfully";
                             $responseCode = 200;
                        } else {
                            $MESSAGE = 'Invalid Vehichle No.';
                            $responseCode = 304;
                        }
                    
                } else {
                    $MESSAGE = MSG302;
                    $responseCode = 302;
                }
               
                $resp = array( 
                            'responseMessage' => $MESSAGE,
                            'responseCode'    => $responseCode
                        );

                if ($rows) {

                    foreach($rows as $row) {
                        $usage = $this->usermodel->calculate_bill_amount($row);
                        if($this->usermodel->is_active_pass($row->pin, $row->vehicle_no, $row->vehicle_model, date('Y-m-d',strtotime($row->checkin_time)),$row->vendor_id)) {
                             $billAmountDetail = array('billAmount' =>0, 'description' => 'Amount is showing 0 INR because of Active Pass User');
                            
                        } else {
                           
                            $billAmountDetail = array('billAmount' =>filter_var($usage['billAmount'], FILTER_SANITIZE_NUMBER_INT), 'description' => '');
                        }

                        $response = array();
                        $response['tokenNumber'] = $row->vehicle_no;
                        $response['pin'] = $row->pin;
                        if($row->mobile) {
                            $response['mobileNumber'] = $row->mobile;
                        }
                        $response['bill'] = $billAmountDetail;
                        $response['durationOccupied'] = $usage['durationOccupied'];
                        $response['checkInTime'] = date("d-m-Y, h:i A", strtotime($row->checkin_time));
                        $response['transactionId'] = $row->checkin_id;
                        $response['vehicleType'] = ($row->vehicle_size == 1 ? 'Bike' : 'Car');
                        $response['fullTokenNumber'] = ($row->vehicle_model);
                       // $response['billAmount'] = filter_var($usage['billAmount'], FILTER_SANITIZE_NUMBER_INT);
                        $resp['tokenDetailList'][] = $response;
                    }
                }
                $this->response($resp, 200);
            } catch (Exception $ex) {
                throw new Exception('Error in VendorLogin function - ' . $ex);
            }
   }

   function checkout_post() {
         try {
                $allowParam = array(
                'otherInfo'
                // 'userId',
                // 'venderId',
                // 'tokenNumber',
                // 'transactionId',
                // 'paymentMode',
                );
                if(is_array($this->post('otherInfo'))) {
                     $post = $this->post('otherInfo');
                } else {
                     $post = json_decode($this->post('otherInfo'),1);
                }
//echo 'hello';
//print_r($post); exit;

                if (checkselectedparams($this->post(), $allowParam)) {
//                    $post = $this->post('otherInfo');
                    $vendor_id = $post['venderId'];
                    $vehicle_no = $post['tokenNumber'];
                    $transactionId = $post['transactionId'];
                    $where = array('checkin_id' => $transactionId, 'is_checkout' => '0');
                    $row = $this->usermodel->get_data('checkin_details', $where);
                    if($row) {
                     //  
                         if($this->usermodel->checkout($post, $row)) {
                            $vendor = $this->get_vendor_details($vendor_id);
                            if($this->usermodel->is_active_pass($row->pin, $row->vehicle_no, $row->vehicle_model, date('Y-m-d',strtotime("+1 day")),$row->vendor_id)) {
                                $plan_details = $this->usermodel->get_pass_details($row->vehicle_no, $vehicle_model, $vendor_id);
                                print_r($plan_details);
                                $digits = 5;
                                $pin = str_pad(rand(0, pow(10, $digits)-1), $digits, '0', STR_PAD_LEFT);
                              //  $days_left = strtotime(date("Y-m-d")) - strtotime(date("Y-m-d",strtotime($
                                $days_left1 = strtotime(date('Y-m-d')) - strtotime(date("Y-m-d",strtotime($plan_details->plan_start_date)));

                                //echo $days_left/60*60*24; echo '<br />';
                                $days_left = $plan_details->plan_duration - ($days_left1/60*60*24);
                            // echo $days_left; exit;
                                $text = "Dear User, Thanks for using ".$plan_details->plan_title." ParkingPass. You have ".($days_left - 1)." days left in ur ParkingPass. For ur next visit, pls use the PIN 5".$pin." at entry gate.";
                             //   echo $text; exit;
                            } else {
                                if($this->is_forcecheckout($row->checkin_id)) {
                                    $text = "Dear User, Ur vehicle ".$vehicle_no." forcefully left parking ".$vendor->vendor_address." at ".date('d-m-Y, h:i A').". Ur unpaid bill is Rs ".$row->bill_amount." for ".$row->duration_occupied." hrs incl taxes.";
                                } else {
                                    $text = "Dear User, Ur vehicle ".$vehicle_no." checked out from parking at ".$vendor->vendor_address." at ".date('d-m-Y, h:i A').". Ur total bill was Rs ".$row->bill_amount." for ".$row->duration_occupied." incl taxes.";
                                } 
                            }
                            $mobile = $row->mobile;
                            if($mobile) {
                                $this->usermodel->send_sms($mobile, $text);
                            }   
                            $MESSAGE = "Check-out Success";
                            $responseCode = 200;   
                         } else {
                            $MESSAGE = "Checkout failed try again!";
                            $responseCode = 304;
                         }
                         
                    } else {
                        $where = array('checkin_id' => $transactionId, 'is_checkout' => '1');
                        $row = $this->usermodel->get_data('checkin_details', $where);
                        if($row) {
                            $MESSAGE = "Already checked-out!";
                            $responseCode = 304;
                        } else {
                           $MESSAGE = "Invalid Vechilce No!";
                            $responseCode = 304; 
                        }
                    }
                } else {
                    $MESSAGE = MSG302;
                    $responseCode = 302;
                }
               
                $resp = array( 
                            'responseMessage' => $MESSAGE,
                            'responseCode'    => $responseCode
                        );

               
                $this->response($resp, 200);
            } catch (Exception $ex) {
                throw new Exception('Error in VendorLogin function - ' . $ex);
            }

   }

   function forgotpin_post() {
       try {
            $allowParam = array(
            'otherInfo'
            );
	    if(is_array($this->post('otherInfo'))) {
                     $post = (object) $this->post('otherInfo');
                } else {
                     $post = json_decode($this->post('otherInfo'));
                }

            if (1) {
                if (isset($_FILES['photo']) && $_FILES['photo']['name'] != '') {
                    $config['upload_path']   = UPLOAD_PATH; 
                    $config['allowed_types'] = 'gif|jpg|png|jpeg'; 
	                $this->load->library('upload', $config);
           /*            print_r($this->upload->do_upload('photo')); 
                      print_r($this->upload->display_errors());
                       exit;*/
                    if ($this->upload->do_upload('photo')) {
                        $uploaded_files = $this->upload->data();
                    } else {
                        $MESSAGE = strip_tags($this->upload->display_errors());
                        $responseCode = 304;
                    }
                }
                $update_res = $this->usermodel->updateProfilePic($uploaded_files['file_name'], $post);
                if($update_res) {
                    $data = $this->usermodel->get_data('checkin_details', array('checkin_id' => $post->transactionId));
                    $vendor = $this->get_vendor_details($data->vendor_id);
                    $text = "Dear User, You parked ur vehicle at ".$vendor->vendor_address." at ".date('d-m-Y, h:i A', strtotime($data->checkin_time)).". For easy check-out from parking, pls use the PIN ".$data->pin." at exit.";
                    $mobile = $post->mobileNumber;
                    $update_res = $this->usermodel->send_sms($mobile, $text);
                    if($update_res) {   
                        $MESSAGE = "PIN has been sent to your mobile number.";
                        $responseCode = 200;   
                     } else {
                        $MESSAGE = "PIN could not be sent.Try again!";
                        $responseCode = 304;
                     }
                 } else {
                    $MESSAGE = "Failed.";
                    $responseCode = 304;
                 }

            } else {
                 $MESSAGE = MSG302;
                  $responseCode = 302;
            }
            $resp = array(
                'responseMessage' => $MESSAGE,
                'responseCode'    => $responseCode,
            );
              
         $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in VendorLogin function - ' . $ex);
        }
    }

    function resetpin_post() {
         try {
            $allowParam = array(
            'transactionId',
            );
            if (checkselectedparams($this->post(), $allowParam)) {
                $data = $this->usermodel->get_data('checkin_details', array('checkin_id' => $this->post('transactionId')));
                $text = 'Your pin is '.$data->pin;
                $mobile = $data->mobile;
                $update_res = $this->usermodel->send_sms($mobile, $text);
                if($update_res) {   
                    $MESSAGE = "PIN has been sent to your mobile number.";
                    $responseCode = 200;   
                 } else {
                    $MESSAGE = "PIN could not be sent. Try again!";
                    $responseCode = 304;
                 }
            } else {
                        $MESSAGE = MSG302;
                        $responseCode = 302;
            }

            $resp = array(
                'responseMessage' => $MESSAGE,
                'responseCode'    => $responseCode,
            );
              
            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in VendorLogin function - ' . $ex);
        }
    }

    function termsAndConditions_post() {
         try {
            $allowParam = array(
            'venderId',
            );
            if (checkselectedparams($this->post(), $allowParam)) {
                $update_res = $this->usermodel->get_data('pages', array('page_id' => 1));
                if($update_res) {
                    $MESSAGE = "Success";
                    $responseCode = 200;   
                 } else {
                    $MESSAGE = "Failure";
                    $responseCode = 304;
                 }
            } else {
                        $MESSAGE = MSG302;
                        $responseCode = 302;
            }

            $resp = array(
                'responseMessage' => $MESSAGE,
                'responseCode'    => $responseCode,
                'termsAndConditions' => $update_res->page_content
            );
              
            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in termsAndConditions function - ' . $ex);
        }
    }

    function getPage_post() 
    {
         try {
            $allowParam = array(
            'page_id',
            );
            if (checkselectedparams($this->post(), $allowParam)) {
                $update_res = $this->usermodel->get_data('pages', array('page_id' => $this->post('page_id')));
                if($update_res) {
                    $MESSAGE = "Success";
                    $responseCode = 200;   
                 } else {
                    $MESSAGE = "Failure";
                    $responseCode = 304;
                 }
            } else {
                        $MESSAGE = MSG302;
                        $responseCode = 302;
            }

            $resp = array(
                'responseMessage' => $MESSAGE,
                'responseCode'    => $responseCode,
                'pageContent' => $update_res->page_content
            );
              
            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in termsAndConditions function - ' . $ex);
        }
    }

     function getplandetails_post() 
     {

        try {
                $allowParam = array(
                'venderId'
                );
                
                if (checkselectedparams($this->post(), $allowParam)) {
                    
                        $vendor_id = $this->post('venderId');
                        $plans = $this->usermodel->get_plan_details($vendor_id);

                        if($plans) {
                             $MESSAGE = "Success";
                             $responseCode = 200;

                        } else {
                            $MESSAGE = 'No Plans Available';
                            $responseCode = 304;
                        }
                    
                } else {
                    $MESSAGE = MSG302;
                    $responseCode = 302;
                }
               
                $resp = array( 
                            'responseMessage' => $MESSAGE,
                            'responseCode'    => $responseCode,
                            'categories' => $plans
                        );

                
                $this->response($resp, 200);
            } catch (Exception $ex) {
                throw new Exception('Error in VendorLogin function - ' . $ex);
            }
   }

   function activate_plan_post() 
    {
            try {
                $allowParam = array(
                'userId',
                'venderId',
                'tokenNumber',
                'mobileNumber',
                'planId',
                'paymentMode'
                );
          
                if (checkselectedparams($this->post(), $allowParam)) {
                    $vendor_id = $this->post('venderId');
                    
                        $this->load->model('backend');
                        
                        $where = array('email' => $this->post('email'));
                        $plan_active = $this->usermodel->is_already_plan($this->post('tokenNumber'), $this->post('venderId'));
                    
                         if(!$plan_active) {
                            $post = $this->post();
                            $data['user_id'] = $this->post('userId');
                            $data['vehicle_no'] = $this->post('tokenNumber');
                            $data['mobile'] = $this->post('mobileNumber');
                            $data['plan_id'] = $this->post('planId');
                            $data['payment_mode'] = $this->post('paymentMode');
                            $plan_detail = $this->usermodel->get_data('vendor_plans', array('plan_id' => $this->post('planId')));
                            $expire_time_db = date("Y-m-d", strtotime("+ ".$plan_detail->plan_duration." days"));
                            $data['plan_end_date'] = $expire_time_db;


                            $res = $this->backend->insert_data($data, 'user_plans');
                            

                            if ($res) {
                                $digits = 5;
                                $pin = str_pad(rand(0, pow(10, $digits)-1), $digits, '0', STR_PAD_LEFT);
                                $vehicle_no = $this->post('tokenNumber');
                                $vendor = $this->get_vendor_details($plan_detail->vendor_id);
                                $expire_time = date("d-m-Y", strtotime("+ ".$plan_detail->plan_duration." days"));
                                $text = "Dear User, Thanks for opting for ".$plan_detail->plan_title." ParkingPass for ".$vendor->vendor_address." for ur vehicle ".$data['vehicle_no'].". The validity of ParkingPass is from ".date('d-m-Y')." to ".$expire_time.". For easy entry at the parking, pls use the PIN 5".$pin." at entry gate. Be assured, we will send you a new PIN for every visit.";
                                $mobile = $data['mobile'];
                                $this->usermodel->send_sms($mobile, $text);

                                $MESSAGE = "$plan_detail->plan_title activated successfully for vehicle number $vehicle_no";
                                $responseCode = 200;
                             } else {
                                $MESSAGE = MSG304;
                                $responseCode = 304;
                             }
                        } else {
                            $MESSAGE = 'Plan already activated.';
                            $responseCode = 304;
                        }
                    
                } else {
                    $MESSAGE = MSG302;
                    $responseCode = 302;
                }
              // $this->usermodel->get_data('users', array(''))
                $resp = array( 
                            'responseMessage' => $MESSAGE,
                            'responseCode'    => $responseCode,
                        );
               
                $this->response($resp, 200);
            } catch (Exception $ex) {
                throw new Exception('Error in VendorLogin function - ' . $ex);
            }
    }

    function is_forcecheckout($checkin_id) {
        $res = $this->db->get_where('checkout_details', array('checkin_id' => $checkin_id))->row();
        if($res->is_force_checkout)
            return true;
        else
            return false;
    }

    function get_vendor_details($vendor_id) {
        $res = $this->db->get_where('vendors', array('vendor_id' => $vendor_id));
        return $vendor = $res->row();
    }
    
    
}
