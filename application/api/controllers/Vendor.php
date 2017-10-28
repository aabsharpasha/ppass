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
                $res = $this->usermodel->login($this->post('userName'), md5($this->post('password')));

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

    /*
     * TO BOOK RESTAURANT TABLE FOR WEB...
     */

    
}
