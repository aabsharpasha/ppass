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
                    $vendor_id = $this->post('venderId');
                    if(strlen($this->post('pin')) != 4) {
                        $MESSAGE = 'Pin must be 4 digit.';
                        $responseCode = 304;
                    } else  if(strlen($this->post('tokenNumber')) != 4) {
                        $MESSAGE = 'Vehicle number must be last 4 digit.';
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
                         if(!$resExist) {
                            $res = $this->usermodel->checkin_vendor_insert($this->post());
                            if ($res) {
                                $mobile = $this->post('mobileNumber');
                                if($mobile) {
                                    if($this->post('tokenNumber'))
                                        $vehicle_no = $this->post('tokenNumber');
                                    else
                                        $vehicle_no = $this->post('fullTokenNumber');
                                    $text = 'Your pin number is '.$this->post('pin').' for vehicle no: '.$vehicle_no;
                                    $update_res = $this->usermodel->send_sms($mobile, $text);
                                }
                                $MESSAGE = "Check-in Success";
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

                if($rows) {
                    foreach($rows as $row) {
                        $usage = $this->usermodel->calculate_bill_amount($row);
                        $response = array();
                        $response['tokenNumber'] = $row->vehicle_no;
                        $response['pin'] = $row->pin;
                        if($row->mobile) {
                            $response['mobileNumber'] = $row->mobile;
                        }
                        $response['billAmount'] = $usage['billAmount'];
                        $response['durationOccupied'] = $usage['durationOccupied'];
                        $response['checkInTime'] = $row->checkin_time;
                        $response['transactionId'] = $row->checkin_id;
                        $response['vehicleType'] = ($row->vehicle_size == 1 ? 'Bike' : 'Car');
                        $response['fullTokenNumber'] = ($row->vehicle_model );

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
                'userId',
                'venderId',
                'tokenNumber',
                'transactionId',
                'paymentMode',
                );
                
                if (checkselectedparams($this->post(), $allowParam)) {
                    $vendor_id = $this->post('venderId');
                    $vehicle_no = $this->post('tokenNumber');
                    $transactionId = $this->post('transactionId');
                    $where = array('checkin_id' => $transactionId);
                    $row = $this->usermodel->get_data('checkin_details', $where);
                    if($row) {
                         if($this->usermodel->checkout($this->post(), $row)) {
                            $MESSAGE = "Check-out Success";
                            $responseCode = 200;   
                         } else {
                            $MESSAGE = "Checkout failed try again!";
                            $responseCode = 304;
                         }
                         
                    } else {
                        $MESSAGE = 'No vehicle found';
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
            'otherInfo'
            );
	    $post = json_decode($this->post('otherInfo'));

            if (1) {
                if (isset($_FILES['photo']) && $_FILES['photo']['name'] != '') {

                   // $post = json_decode(json_encode($this->post('otherInfo')));
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
//print_r($post); exit;
                $update_res = $this->usermodel->updateProfilePic($uploaded_files['file_name'], $post);

                if($update_res) {
                    $data = $this->usermodel->get_data('checkin_details', array('checkin_id' => $post->transactionId));
                    $text = 'Your pin is '.$data->pin.' for vechicle no: '.$data->vehicle_no;
                    $mobile = $post->mobileNumber;
//print_r($post->mobileNumber); exit;
                    $update_res = $this->usermodel->send_sms($mobile, $text);
                    if($update_res) {   
                        $MESSAGE = "Pin has been sent to your mobile number.";
                        $responseCode = 200;   
                     } else {
                        $MESSAGE = "Pin could not be sent.Try again!";
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
                    $MESSAGE = "Pin has been sent to your mobile number.";
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

    function termsAndConditions_post() {
         try {
            $allowParam = array(
            'venderId',
            );
            if (checkselectedparams($this->post(), $allowParam)) {
                $update_res = $this->usermodel->get_data('pages', array('vendor_id' => $this->post('venderId')));
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
    
    
}
