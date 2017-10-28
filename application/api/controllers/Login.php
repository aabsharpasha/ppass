<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {
        function __construct() {
          parent::__construct();
        }
	public function index()
	{
            $user_name = $this->input->post('user_name');
            $password  = md5($this->input->post('password'));
            $this->user_login($user_name,$password);
	}
        
        public function user_login($user_name=NULL,$password=NULL)
	{
            $login = $this->user->login($user_name,$password);
            unset($login['user_data']->password);
              if($login['status'] == 1){
                $this->session->set_userdata('active_user', $login['user_data']);
                if($login['user_data']->user_type == 'admin'){
                  redirect(base_url('admin/excel_upload'));
                } else {
                  redirect(base_url('listing'));
                }
              } else {
                $this->session->set_flashdata('login_msg','Username or password is incorrect.');
                redirect(base_url());
              }
	}
        
        public function user_logout()
	{
            $this->session->unset_userdata('active_user');
            redirect(base_url());
	}
}
