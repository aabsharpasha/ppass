<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
require(APPPATH . '/libraries/REST_Controller.php');

/**
 * Description of login
 * @author Victor Technolabs
 */
class Customer extends REST_Controller {

    function __construct() {

        parent::__construct();

        $this->load->model('usermodel');
    }
    function login_post() 
    {
            try {
                $allowParam = array(
                'email',
                'password'
                );
         
                if (checkselectedparams($this->post(), $allowParam)) {
                    $res = $this->usermodel->login($this->post('email'), md5($this->post('password')));

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
                        'responseCode'    => $responseCode,
                        'userData' => $res
                     );
           
                $this->response($resp, 200);
            } catch (Exception $ex) {
                throw new Exception('Error in VendorLogin function - ' . $ex);
            }
    }

    function register_post() 
    {
            try {
                $allowParam = array(
                'user_name',
                'password',
                'email'
                );
          
                if (checkselectedparams($this->post(), $allowParam)) {
                    $vendor_id = $this->post('venderId');
                    
                        $this->load->model('backend');
                        
                        $where = array('email' => $this->post('email'));
                        $resExist = $this->userauth->is_exist_data('users', $where);
                    
                         if(!$resExist) {
                            $data = $this->post();
                            $data['password'] = md5($this->post('password'));
                            $res = $this->backend->insert_data($data, 'users');
                          // print_r($res); exit;
                            $user = $this->usermodel->get_userdata($res);

                            if ($res) {
                                $MESSAGE = "Registration Success";
                                $responseCode = 200;
                             } else {
                                $MESSAGE = MSG304;
                                $responseCode = 304;
                             }
                        } else {
                            $MESSAGE = 'User Email already exist.';
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
                            'userData' => $user
                        );
               
                $this->response($resp, 200);
            } catch (Exception $ex) {
                throw new Exception('Error in VendorLogin function - ' . $ex);
            }
    }
       
    function getNearByVendors_post() 
    {
        try {
            $allowParam = array(
            'userLat',
            'userLong',
            );
            if (checkselectedparams($this->post(), $allowParam)) {
                $userLat = $this->post('userLat');
                $userLong = $this->post('userLong');

                $vendors = $this->usermodel->getNearByVendors($userLat, $userLong);
                if($vendors) {
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

            foreach($vendors as $line) {
                $line->pricing_details = $this->usermodel->getPricingDetails($line->vendor_id);
                $vendorsList[] = $line;
            }

            $resp = array(
                'responseMessage' => $MESSAGE,
                'responseCode'    => $responseCode,
                'vendorsList' => $vendorsList
            );
              
            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in getNearByVendors_post function - ' . $ex);
        }
    }

    function registerVehicle_post() 
    {
            try {
                $allowParam = array(
                'vehicle_number',
                'vehicle_type',
                'user_id'
                );
          
                if (checkselectedparams($this->post(), $allowParam)) {
                    if(strlen($this->post('vehicle_number')) != 4) {
                        $MESSAGE = 'Vehicle number must be last 4 digit.';
                        $responseCode = 304;
                    } else {
                            $where = array('vehicle_number' => $this->post('vehicle_number'), 'user_id' => $this->post('user_id'));
                            $resExist = $this->userauth->is_exist_data('user_vehicles', $where);
                        
                         if(!$resExist) {
                            $res = $this->usermodel->add_vehicle($this->post());
                            if ($res) {
                                $vehicleList = $this->usermodel->getVehiclesListByUser($this->post('user_id'));
                                $MESSAGE = "Vechicle Added";
                                $responseCode = 200;
                             } else {
                                $MESSAGE = MSG304;
                                $responseCode = 304;
                             }
                        } else {
                            $MESSAGE = 'Vehichle already added.';
                            $responseCode = 304;
                        }
                    }
                } else {
                    $MESSAGE = MSG302;
                    $responseCode = 302;
                }
               
                $resp = array( 
                            'responseMessage' => $MESSAGE,
                            'responseCode'    => $responseCode,
                            'vehilceList' => $vehicleList
                        );
               
                $this->response($resp, 200);
            } catch (Exception $ex) {
                throw new Exception('Error in VendorLogin function - ' . $ex);
            }
    }

    function getVehiclesList_post() 
    {
            try {
                $allowParam = array(
                    'user_id'
                );
          
                if (checkselectedparams($this->post(), $allowParam)) {
                            $vehicleList = array();
                            $vehicleList = $this->usermodel->getVehiclesListByUser($this->post('user_id'));
                            if ($vehicleList) {
                               
                                $MESSAGE = "Vechicle List Populated";
                                $responseCode = 200;
                             } else {
                                $MESSAGE = MSG304;
                                $responseCode = 304;
                             }
                } else {
                    $MESSAGE = MSG302;
                    $responseCode = 302;
                }
               
                $resp = array( 
                            'responseMessage' => $MESSAGE,
                            'responseCode'    => $responseCode,
                            'vehilceList' => $vehicleList
                        );
               
                $this->response($resp, 200);
            } catch (Exception $ex) {
                throw new Exception('Error in VendorLogin function - ' . $ex);
            }
    }

    function booking_post() 
    {
            try {
                $allowParam = array(
                'user_id',
                'vendor_id',
                'vehicle_no',
                'start_time',
                'end_time',
                'payment_mode',
                'vehicle_size',
                );
          
                if (checkselectedparams($this->post(), $allowParam)) {
                        $vendor_id = $this->post('vendor_id');
                        $this->load->library('userauth');
                        $vechicle_size = $this->post('vehicle_size');
                       
                        $where = array('vehicle_no' => $this->post('vehicle_no'), 'is_checkout' => '0', 'vehicle_size' => $vechicle_size, 'vendor_id' => $vendor_id);
                        $resExist = $this->userauth->is_exist_data('checkin_details', $where);
                        
                        if(!$resExist) {
                            $res = $this->usermodel->customer_booking_save($this->post());
                            if ($res) {
                                $mobile = $this->post('mobile');
                                if($mobile) {
                                    $text = 'Congrats! Slot Booked. Your pin number is '.$res.' for vehicle no: '.$this->post('vehicle_no');
                                    $update_res = $this->usermodel->send_sms($mobile, $text);
                                }
                                $MESSAGE = "Slot has been booked.";
                                $responseCode = 200;
                             } else {
                                $MESSAGE = MSG304;
                                $responseCode = 304;
                             }
                        } else {
                            $MESSAGE = 'Vehichle already in parking.';
                            $responseCode = 304;
                        }
                    
                } else {
                    $MESSAGE = MSG302;
                    $responseCode = 302;
                }
               
                $resp = array( 
                            'responseMessage' => $MESSAGE,
                            'responseCode'    => $responseCode,
                            'pin' => $res
                        );
               
                $this->response($resp, 200);
            } catch (Exception $ex) {
                throw new Exception('Error in VendorLogin function - ' . $ex);
            }
    }
    
}
