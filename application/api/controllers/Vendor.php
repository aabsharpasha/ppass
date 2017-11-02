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
            /*
             * TO SET DEFAULT VARIABLE VALUES...
             */
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;

            if (checkselectedparams($this->post(), $allowParam)) {
                $MESSAGE = NO_RECORD_FOUND;
                $STATUS = FAIL_STATUS;
                $res = $this->usermodel->login($this->post('userName'), md5($this->post('password')), $this->post('vendorId'));

                if (!empty($res)) {
                    $MESSAGE = "Logged in Successfully";
                    $STATUS = SUCCESS_STATUS;
                }
            }

            $resp = array( 
                        'responseMessage' => $MESSAGE,
                        'status'          => $STATUS,
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
            /*
             * TO SET DEFAULT VARIABLE VALUES...
             */
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;

            if (checkselectedparams($this->post(), $allowParam)) {
                

                if(strlen($this->post('pin')) != 4) {
                    $res = 0;
                    $MESSAGE = 'Pin must be 4 digit.';
                    $STATUS = FAIL_STATUS;
                } else  {
                    $res = $this->usermodel->checkin_vendor_insert($this->post());
                    if($res == 0) {
                        $MESSAGE = 'Pin already exist. Please select another pin.';
                        $STATUS = FAIL_STATUS;
                    }
                }
                

                if ($res) {
                    $MESSAGE = "Checked In Successfully";
                    $STATUS = SUCCESS_STATUS;
                }
            }

            $resp = array( 
                        'responseMessage' => $MESSAGE,
                        'status'          => $STATUS,
                    );
           
           
            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in VendorLogin function - ' . $ex);
        }
    }

    /*
     * TO BOOK RESTAURANT TABLE FOR WEB...
     */

    
}
