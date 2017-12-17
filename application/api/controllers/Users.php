<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
require(APPPATH . '/libraries/REST_Controller.php');

/**
 * Description of login
 */
class Users extends REST_Controller {

    function __construct() {

        parent::__construct();

        $this->load->model('usermodel');
    }

    function getUserDetails_post() {
        try {

            $allowParam = array(
                'userId'
            );
            /*
             * TO SET DEFAULT VARIABLE VALUES...
             */
            $MESSAGE = INSUFF_DATA;
            $STATUS = FAIL_STATUS;
            $CARTDATA = '';

            if (checkselectedparams($this->post(), $allowParam)) {

                /*
                 * SEARCH FROM THE MODEL TO GET THE RESULT...
                 */
                $MESSAGE = NO_RECORD_FOUND;
                $STATUS = FAIL_STATUS;

                $res = $this->usermodel->getUserDetails($this->post('userId'));

                if (!empty($res)) {
                    $MESSAGE = RESTAURANT_FOUND;
                    $STATUS = SUCCESS_STATUS;
                    $USERDATA = $res;
                }
            }

            $resp = array(
                'MESSAGE' => $MESSAGE,
                'STATUS' => $STATUS
            );

            if (@$USERDATA != '') {
                $resp['USERDATA'] = $USERDATA;
            }

            $this->response($resp, 200);
        } catch (Exception $ex) {
            throw new Exception('Error in getUserDetails_post function - ' . $ex);
        }
    }

    /*
     * TO BOOK RESTAURANT TABLE FOR WEB...
     */

    
}
