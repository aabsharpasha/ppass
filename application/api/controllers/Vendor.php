<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
require(APPPATH . '/libraries/REST_Controller.php');

/**
 * Description of login
 * @author OpenXcell Technolabs
 */
class Vendor extends REST_Controller {

    function __construct() {

        parent::__construct();

        $this->load->model('usermodel');
    }

    function login_post() {
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


    function checkin_vendor_post() {
            try {
                $allowParam = array(
                'userId',
                'venderId',
                'tokenNumber',
                'pin',
                'goodsSize'
                );
          
                if (checkselectedparams($this->post(), $allowParam)) {
                    if(strlen($this->post('pin')) != 4) {
                        $MESSAGE = 'Pin must be 4 digit.';
                        $responseCode = 304;
                    } else  if(strlen($this->post('tokenNumber')) != 4) {
                        $MESSAGE = 'Vehicle number must be last 4 digit.';
                        $responseCode = 304;
                    } else {
                        $this->load->library('userauth');
                        $where = array('vehicle_no' => $this->post('tokenNumber'), 'is_checkout' => '0');
                        if(!$this->userauth->is_exist_data('checkin_details', $where)) {

                            $res = $this->usermodel->checkin_vendor_insert($this->post());
                            if ($res) {
                                $MESSAGE = "Checked In Successfully";
                                $responseCode = 200;
                             } else {
                                $MESSAGE = MSG304;
                                $responseCode = 304;
                             }
                        } else {
                            $MESSAGE = 'Vehichle already in parking.';
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
                        $row = $this->usermodel->get_data('checkin_details', $where);
                        if($row) {
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

                if($row) {
                    $usage = $this->usermodel->calculate_bill_amount($row);
                    $resp['tokenNumber'] = $row->vehicle_no;
                    $resp['pin'] = $row->pin;
                    if($row->mobile) {
                        $resp['mobileNumber'] = $row->mobile;
                    }
                    $resp['billAmount'] = $usage['billAmount'];
                    $resp['durationOccupied'] = $usage['durationOccupied'];
                    $resp['checkInTime'] = $row->checkin_time;
                    $resp['transactionId'] = $row->checkin_id;
                }
                $this->response($resp, 200);
            } catch (Exception $ex) {
                throw new Exception('Error in VendorLogin function - ' . $ex);
            }
   }

   function checkout_post() {
         try {
                $allowParam = array(
                'userId',
                'venderId',
                'tokenNumber',
                'transactionId',
                'paymentMode',
                );
                
                if (checkselectedparams($this->post(), $allowParam)) {
                    $vendor_id = $this->post('venderId');
                    $vehicle_no = $this->post('tokenNumber');
                    $where = array('vendor_id' => $vendor_id, 'vehicle_no' => $vehicle_no);
                    $row = $this->usermodel->get_data('checkin_details', $where);
                    if($row) {
                         if($this->usermodel->checkout($this->post(), $row)) {
                            $MESSAGE = "Checkout completed.";
                            $responseCode = 200;   
                         } else {
                            $MESSAGE = "Checkout failed try again!";
                            $responseCode = 304;
                         }
                         
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

               
                $this->response($resp, 200);
            } catch (Exception $ex) {
                throw new Exception('Error in VendorLogin function - ' . $ex);
            }

   }

   function forgotpin_post() {
       try {
            $allowParam = array(
            'transactionId',
            'mobileNumber'
            );
            if (checkselectedparams($this->post(), $allowParam)) {
                   
                if (isset($_FILES['photo']) && $_FILES['photo']['name'] != '') {

                    $config['upload_path']   = UPLOAD_PATH; 
                    $config['allowed_types'] = 'gif|jpg|png|jpeg'; 
                    // $config['max_size']      = 4000; 
                    // $config['max_width']     = 1024; 
                    // $config['max_height']    = 768;  
                    $this->load->library('upload', $config);
                      // print_r($this->upload->do_upload('photo')); 
                      // print_r($this->upload->display_errors());
                      // exit;
                
               
                    if ($this->upload->do_upload('photo')) {
                        $uploaded_files = $this->upload->data();
                        $update_res = $this->usermodel->updateProfilePic($uploaded_files['file_name'], $this->post());
                        if($update_res) {
                              $MESSAGE = "Data captured.";
                            $responseCode = 200;   
                         } else {
                            $MESSAGE = "Data not captured.";
                            $responseCode = 304;
                         }
                    } else {
                        $MESSAGE = strip_tags($this->upload->display_errors());
                        $responseCode = 304;
                    }
                        
                        
                } else {
                        $MESSAGE = 'Error in photo';
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
                $update_res = $this->usermodel->send_sms($this->post());
                if($update_res) {
                    $first_one = substr($update_res->mobile, 0, 1);
                    $last_three = substr($update_res->mobile, 7, 3);
                    $mobile_new = $first_one."xxxxxx".$last_three;
                    $MESSAGE = "Pin has been sent to your mobile number(".$mobile_new.").";
                    $responseCode = 200;   
                 } else {
                    $MESSAGE = "Pin could not be sent. Try again!";
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
    
    
}
