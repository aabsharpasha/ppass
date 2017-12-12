<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Vendor extends CI_Controller {
        function __construct() {
          parent::__construct();
         // echo $this->uri->segment(2); exit;
          $exclude_pages = array('download_reciept','get_reciept_list', 'download_pdf');
          if(!in_array($this->uri->segment(2), $exclude_pages)) {
            $this->userauth->authentication('normal');
          }
          
        }

       
        function get_vendor_details($vendor_id) {
          $res = $this->db->get_where('vendors', array('vendor_id' => $vendor_id));
          return $res->row();
        }

        public function index($arg=NULL) {
                  $this->load->view('includes/header_admin');
                  $this->load->view('includes/sidebar_admin');          
                  $this->load->view('vendor/list');
                  $this->load->view('includes/footer.php');
        }

        public function users($arg=NULL) {
                  $this->load->view('includes/header_admin');
                  $this->load->view('includes/sidebar_admin');          
                  $this->load->view('vendor/userslist');
                  $this->load->view('includes/footer.php');
        }

         public function pricing($arg=NULL) {
                  $this->load->view('includes/header_admin');
                  $this->load->view('includes/sidebar_admin');          
                  $this->load->view('vendor/pricinglist');
                  $this->load->view('includes/footer.php');
        }
        
        function get_reciept_list() {
          parse_str($this->input->post('data'));
          $where = array('vendor_id' => $vendor_id, 'pin' => $vehicle_pin, 'is_checkout' => '1');
         $this->db->select('checkin_details.checkin_id, checkin_details.vehicle_no, checkout_details.duration_occupied, checkin_details.checkin_time, checkout_details.checkout_time, checkout_details.paid_amount, checkout_details.checkout_time')
         ->from('checkin_details')
         ->join('checkout_details', 'checkin_details.checkin_id = checkout_details.checkin_id')->where($where);
         $this->db->where('(vehicle_model = "'.$vehicle_number.'" OR vehicle_no = "'.$vehicle_number.'")');
       //  echo "hi".$this->db->last_query(); exit;
          $result = $this->db->get();
          $res = $result->result();
          if($res) {
            $location = $this->get_vendor_details($vendor_id);
            foreach($res as $line) {
              $ret_str .= '<li class="list-download">';
              $ret_str .= "<div><span>Vehicle No: </span> ".$line->vehicle_no."</div>";
              $ret_str .= "<div><span>Location: </span> ".$location->vendor_address."</div>";
              $ret_str .= "<div><span>Duration: </span> ".$line->duration_occupied."</div>";
              $ret_str .= "<div><span>Check In time: </span> ".date("Y-m-d h:i A",strtotime($line->checkin_time))."</div>";
              $ret_str .= "<div><span>Check Out time: </span> ".date("Y-m-d h:i A",strtotime($line->checkout_time))."</div>";
              $ret_str .= "<div><span>Amount Paid: </span> ".$line->paid_amount."</div>";
              $ret_str .= "<div><a target='_blank' href=".base_url('vendor/download_pdf/'.$line->checkin_id.'/'.$vendor_id).">Download</a></div>";
              $ret_str .= '</li>';
             
            }
            // $ret_str .= '</ul>';
          } else {
            $ret_str = 'No Match Found.';
          }

          echo '<ul>'.$ret_str.'</ul>'; exit;
          
        }

        function download_reciept() 
        {
                    $this->load->library('form_validation');
                    $this->form_validation->set_rules('user_name', 'User Name', 'trim|required|is_unique[users.user_name]', array(
                        'is_unique'     => 'This %s already exists.'
                        )
                     );

                    $this->form_validation->set_rules('vendor_id', 'Vendor', 'trim|required');
                    $this->form_validation->set_rules('password', 'Password', 'trim|required');
                    if ($this->form_validation->run() === TRUE)
                    {
                        $this->load->model('backend');
                        $data = $this->input->post();
                        $data['password'] = md5($data['password']);
                        $data['user_type'] = 'vendor';
                        if($this->backend->insert_data($data, 'users')) {
                          $this->session->set_flashdata('vendor_add_msg', 'User Created Successfully');
                          redirect('vendor/users');
                        } else {
                          redirect('vendor/users');
                          $this->session->set_flashdata('vendor_add_msg', 'Error while adding...Try Again');
                        }

                    }
                    else
                    {
                          $this->load->view('includes/header_admin', array('hide' => 1));
                          //$this->load->view('includes/sidebar_admin');  
                          $this->load->model('Backend');
                          $view_data['vendors'] = $this->Backend->get_data('vendors');
                          //print_r($view_data); exit;
                          $this->load->view('vendor/download_recipet', $view_data);
                          $this->load->view('includes/footer.php');
                    }
        }

        function download_pdf($id, $vendor_id) 
        {
            // As PDF creation takes a bit of memory, we're saving the created file in /downloads/reports/
                    //echo FCPATH; exit;
                    $filename = "ppass_".time();
                    $pdfFilePath = FCPATH."downloads/reports/$filename.pdf";
                    $data['page_title'] = 'PPASS'; // pass data to the view

                    if (file_exists($pdfFilePath) == FALSE)
                    {

                     // ini_set('memory_limit','32M'); // boost the memory limit if it's low ;)
        
        $where = array('checkin_details.checkin_id' => $id);
         $this->db->select('checkin_details.checkin_id, checkin_details.vehicle_no, checkin_details.vehicle_model, checkout_details.duration_occupied, checkin_details.checkin_time, checkout_details.checkout_time, checkout_details.paid_amount, checkout_details.checkout_time')
         ->from('checkin_details')
         ->join('checkout_details', 'checkin_details.checkin_id = checkout_details.checkin_id')->where($where);
          $result = $this->db->get();
             
          $res = $result->row();
                      $data['checkin_details'] = $res;
                      $data['vendor_location'] =  $this->get_vendor_details($vendor_id);
                     // $this->load->view('pdf_report', $data);
                  // print_r($data);
                      $html = $this->load->view('pdf_report', $data, true); // render the view into HTML
                      // exit;
                      $this->load->library('pdf');

                      $pdf = $this->pdf->load();

                      $pdf->SetFooter($_SERVER['HTTP_HOST'].'|{PAGENO}|'.date(DATE_RFC822)); // Add a footer for good measure ;)

                      $pdf->WriteHTML($html); // write the HTML into the PDF

                      $pdf->Output($pdfFilePath, 'F'); // save to file because we can

                    }

                    redirect("/downloads/reports/$filename.pdf");
        }

        function add_user() {

          //print_r( $this->input->post()); exit;
            $this->load->library('form_validation');
            $this->form_validation->set_rules('user_name', 'User Name', 'trim|required|is_unique[users.user_name]', array(
                'is_unique'     => 'This %s already exists.'
                )
             );

            $this->form_validation->set_rules('vendor_id', 'Vendor', 'trim|required');
            $this->form_validation->set_rules('password', 'Password', 'trim|required');
            if ($this->form_validation->run() === TRUE)
            {
                $this->load->model('backend');
                $data = $this->input->post();
                $data['password'] = md5($data['password']);
                $data['user_type'] = 'vendor';
                if($this->backend->insert_data($data, 'users')) {
                  $this->session->set_flashdata('vendor_add_msg', 'User Created Successfully');
                  redirect('vendor/users');
                } else {
                  redirect('vendor/users');
                  $this->session->set_flashdata('vendor_add_msg', 'Error while adding...Try Again');
                }

            }
            else
            {
                  $this->load->view('includes/header_admin');
                  $this->load->view('includes/sidebar_admin');  
                  $this->load->model('Backend');
                  $view_data['vendors'] = $this->Backend->get_data('vendors');
                  //print_r($view_data); exit;
                  $this->load->view('vendor/add_user', $view_data);
                  $this->load->view('includes/footer.php');
            }
        }
        function is_exist($user_name) {
           $this->load->model('backend');
           $where = array('user_id !=' => $this->input->post('user_id'), 'user_name' => $this->input->post('user_name'));
                        $user = $this->backend->get_data_by_cond('users', $where);
                         if($user->user_id) 
                          return FALSE;
                        else  {
                          $this->form_validation->set_message('username_check', 'Username already exist.');
                          return TRUE;
                        }
        }

        function edit_user($user_id = '') {
            $this->load->model('backend');
            $this->load->library('form_validation');
            $this->load->library('form_validation');
            
                      //  print_r($user); exit;
          
            $this->form_validation->set_rules('user_name', 'User Name', 'callback_is_exist');
            

            $this->form_validation->set_rules('vendor_id', 'Vendor', 'trim|required');
            $this->form_validation->set_rules('password', 'Password', 'trim|required');
            if ($this->form_validation->run() === TRUE)
            {
                
                $data = $this->input->post();
               // $data['vendor_id'] = $vendor_id;
               // $data['vendor_id'] = 'sda700';
                $data['password'] = md5($data['password']);
                $where = array('user_id' => $this->input->post('user_id'));
                if($this->backend->update_data($data, 'users', $where)) {
                  //echo $this->db->last_query(); exit;
                  $this->session->set_flashdata('vendor_add_msg', 'User updated Successfully');
                  redirect('vendor/users');
                } else {
                  redirect('vendor/users');
                  $this->session->set_flashdata('vendor_add_msg', 'Error while adding...Try Again');
                }

            }
            else
            {
                        $where = array('user_id' => $user_id);
                        $view_data['user'] = $this->backend->get_data_by_cond('users', $where);
                        $view_data['vendors'] = $this->backend->get_data('vendors');
                      //  print_r($vendor); exit;
                        $this->load->view('includes/header_admin');
                        $this->load->view('includes/sidebar_admin');          
                        $this->load->view('vendor/add_user', $view_data);
                        $this->load->view('includes/footer.php');
            }
        }

        function delete_user($user_id) {
          $this->load->model('backend');
           $where = array('user_id' => $user_id);
            if($this->backend->delete_data('users', $where)) {
                  //echo $this->db->last_query(); exit;
                  $this->session->set_flashdata('vendor_add_msg', 'User deleted Successfully');
                  redirect('vendor/users');
                } else {
                  redirect('vendor/users');
                  $this->session->set_flashdata('vendor_add_msg', 'Error while adding...Try Again');
                }
        }

        function get_json_data_user() { 
            $limit = $this->input->post('length');
            $start = $this->input->post('start');
            $draw  = $this->input->post('draw');
            $search = $this->input->post('search[value]');
            if(!empty($search)){
              $start = 0;
            }
             $cond = array('user_type' => 'vendor');
            //echo '<pre>'; print_r($this->backend->get_data('excel_data', $limit, $start)); exit;
            $total_rows = $this->backend->get_row_count('users', $search, $cond);
           // print_r($total_rows); exit;
            $this->load->model('User');
            $res = array();
            $result = $this->backend->get_data('users', $limit, $start, $search, $cond);
            $i = 0; $j = $start+1;
            if(!empty($result)){
              foreach($result as $row){
               $vendor = $this->user->get_data('vendors', array('vendor_id' => $row->vendor_id));
                $res[$i][] = $j;
                $res[$i][] = $vendor->vendor_name.' ('.$row->vendor_id.')';
                $res[$i][] = $row->user_name;
                $res[$i][] = $row->email;
              $res[$i][] = '<a href="'.base_url('vendor/edit_user/'.$row->user_id).'">Edit</a> | <a href="'.base_url('vendor/delete_user/'.$row->user_id).'">Delete</a>';
                
                $i++; $j++;
              }
            }
           // echo '<pre>'; print_r($res); exit;
            $data = array('draw' => $draw, 'recordsTotal' => $total_rows, 'recordsFiltered' => $total_rows, 'data' => $res);
            echo json_encode($data); exit;
        }


        function add_pricing() {

          //print_r( $this->input->post()); exit;
            $this->load->library('form_validation');
            
              $this->form_validation->set_rules('vendor_id', 'Vendor Pricing Details', 'trim|required|is_unique[pricing_details.vendor_id]', array(
                'is_unique'     => 'This %s already exists.'
                )
             );
             $this->form_validation->set_rules('big_inventory', 'Big Inventory', 'trim|required');
             $this->form_validation->set_rules('big_first_hours', 'Big Initial Hour', 'trim|required');
             $this->form_validation->set_rules('big_first_hr_rate', 'Big Initial Hour Rate', 'trim|required');
           

            if ($this->form_validation->run() === TRUE)
            {
                $this->load->model('backend');
                $data = $this->input->post();
                //$data['password'] = md5($data['password']);
                if($this->backend->insert_data($data, 'pricing_details')) {
                  $this->session->set_flashdata('vendor_add_msg', 'Pricing Created Successfully');
                  redirect('vendor/pricing');
                } else {
                  redirect('vendor/pricing');
                  $this->session->set_flashdata('vendor_add_msg', 'Error while adding...Try Again');
                }

            }
            else
            {
                  $this->load->view('includes/header_admin');
                  $this->load->view('includes/sidebar_admin');  
                  $this->load->model('Backend');
                  $view_data['vendors'] = $this->Backend->get_data('vendors');
                  //print_r($view_data); exit;
                  $this->load->view('vendor/add_pricing', $view_data);
                  $this->load->view('includes/footer.php');
            }
        }

        function edit_pricing($pricing_id = '') {
            $this->load->model('backend');
            $this->load->library('form_validation');
              
              
             $this->form_validation->set_rules('big_inventory', 'Big Inventory', 'trim|required');
             $this->form_validation->set_rules('big_first_hours', 'Big Initial Hour', 'trim|required');
             $this->form_validation->set_rules('big_first_hr_rate', 'Big Initial Hour Rate', 'trim|required');
           
            if ($this->form_validation->run() === TRUE)
            {
                
                $data = $this->input->post();
               // $data['vendor_id'] = $vendor_id;
               // $data['vendor_id'] = 'sda700';
                $where = array('pricing_id' => $this->input->post('pricing_id'));
                if($this->backend->update_data($data, 'pricing_details', $where)) {
                  //echo $this->db->last_query(); exit;
                  $this->session->set_flashdata('vendor_add_msg', 'Pricing Details updated Successfully');
                  redirect('vendor/pricing');
                } else {
                  redirect('vendor/pricing');
                  $this->session->set_flashdata('vendor_add_msg', 'Error while adding...Try Again');
                }

            }
            else
            {
                        $where = array('pricing_id' => $pricing_id);
                        $view_data['pricing'] = $this->backend->get_data_by_cond('pricing_details', $where);
                        $view_data['vendors'] = $this->backend->get_data('vendors');
                      //  print_r($vendor); exit;
                        $this->load->view('includes/header_admin');
                        $this->load->view('includes/sidebar_admin');          
                        $this->load->view('vendor/add_pricing', $view_data);
                        $this->load->view('includes/footer.php');
            }
        }

        function delete_pricing($user_id) {
          $this->load->model('backend');
           $where = array('user_id' => $user_id);
            if($this->backend->delete_data('users', $where)) {
                  //echo $this->db->last_query(); exit;
                  $this->session->set_flashdata('vendor_add_msg', 'User deleted Successfully');
                  redirect('vendor/users');
                } else {
                  redirect('vendor/users');
                  $this->session->set_flashdata('vendor_add_msg', 'Error while adding...Try Again');
                }
        }

        function get_json_data_pricing() { 
            $limit = $this->input->post('length');
            $start = $this->input->post('start');
            $draw  = $this->input->post('draw');
            $search = $this->input->post('search[value]');
            if(!empty($search)){
              $start = 0;
            }
            //echo '<pre>'; print_r($this->backend->get_data('excel_data', $limit, $start)); exit;
            $total_rows = $this->backend->get_row_count('users', $search);
            $this->load->model('User');
            $res = array();
            $result = $this->backend->get_data('pricing_details', $limit, $start, $search);
            $i = 0; $j = $start+1;
            if(!empty($result)){
              foreach($result as $row){
               $vendor = $this->user->get_data('vendors', array('vendor_id' => $row->vendor_id));
                $res[$i][] = $j;
                $res[$i][] = $vendor->vendor_name.' ('.$row->vendor_id.')';
                $res[$i][] = $row->big_inventory;
                $res[$i][] = "First ".$row->big_first_hours." Hr - "."Rs. ".$row->big_first_hr_rate." | After Rs: ".$row->big_hourly_rate;
                $res[$i][] = $row->small_inventory;
                $res[$i][] = "First ".$row->small_first_hours." Hr - "."Rs. ".$row->small_first_hr_rate." | After Rs: ".$row->small_hourly_rate;
                $res[$i][] = '<a href="'.base_url('vendor/edit_pricing/'.$row->pricing_id).'">Edit</a> | <a href="'.base_url('vendor/delete_pricing/'.$row->pricing_id).'">Delete</a>';
                
                $i++; $j++;
              }
            }
           // echo '<pre>'; print_r($res); exit;
            $data = array('draw' => $draw, 'recordsTotal' => $total_rows, 'recordsFiltered' => $total_rows, 'data' => $res);
            echo json_encode($data); exit;
        }


        function get_json_data(){ //echo '<pre>'; print_r($_GET); exit;
            $limit = $this->input->post('length');
            $start = $this->input->post('start');
            $draw  = $this->input->post('draw');
            $search = $this->input->post('search[value]');
            if(!empty($search)){
              $start = 0;
            }
            //echo '<pre>'; print_r($this->backend->get_data('excel_data', $limit, $start)); exit;
            $total_rows = $this->backend->get_row_count('vendors', $search);
            
            $res = array();
            $result = $this->backend->get_data('vendors', $limit, $start, $search);
            $i = 0; $j = $start+1;
            if(!empty($result)){
              foreach($result as $row){
                $res[$i][] = $j;
                $res[$i][] = $row->vendor_id;
                $res[$i][] = $row->vendor_name;
                $res[$i][] = $row->vendor_address;
             $res[$i][] = '<a href="'.base_url('vendor/edit_vendor/'.$row->vendor_id).'">Edit</a> | <a href="'.base_url('vendor/delete_vendor/'.$row->vendor_id).'">Delete</a>';
                
                $i++; $j++;
              }
            }
           // echo '<pre>'; print_r($res); exit;
            $data = array('draw' => $draw, 'recordsTotal' => $total_rows, 'recordsFiltered' => $total_rows, 'data' => $res);
            echo json_encode($data); exit;
        }

        function add_vendor() {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('vendor_name', 'Vendor Name', 'trim|required');
            $this->form_validation->set_rules('vendor_address', 'Vendor Address', 'trim|required');
            $this->form_validation->set_rules('vendor_lat', 'Vendor Lat', 'trim|required');
            $this->form_validation->set_rules('vendor_long', 'Vendor Long', 'trim|required');
            $this->form_validation->set_rules('vendor_email', 'Vendor Email', 'trim|valid_email');
            if ($this->form_validation->run() === TRUE)
            {
                $this->load->model('backend');
                $data = $this->input->post();
                $data['vendor_id'] = substr($this->input->post('vendor_name'), 0, 3).mt_rand(001, 999);
               // $data['vendor_id'] = 'sda700';
                if($this->backend->insert_data($data, 'vendors')) {
                  $this->session->set_flashdata('vendor_add_msg', 'Vendor Created Successfully');
                  redirect('vendor');
                } else {
                  redirect('vendor');
                  $this->session->set_flashdata('vendor_add_msg', 'Error while adding Vendor...Try Again');
                }

            }
            else
            {
                  $this->load->view('includes/header_admin');
                        $this->load->view('includes/sidebar_admin');          
                        $this->load->view('vendor/add_vendor');
                        $this->load->view('includes/footer.php');
            }
        }

        function edit_vendor($vendor_id) {
            $this->load->model('backend');
            $this->load->library('form_validation');
            $this->form_validation->set_rules('vendor_name', 'Vendor Name', 'trim|required');
            $this->form_validation->set_rules('vendor_address', 'Vendor Address', 'trim|required');
            $this->form_validation->set_rules('vendor_lat', 'Vendor Lat', 'trim|required');
            $this->form_validation->set_rules('vendor_long', 'Vendor Long', 'trim|required');
            $this->form_validation->set_rules('vendor_email', 'Vendor Email', 'trim|valid_email');
            if ($this->form_validation->run() === TRUE)
            {
                
                $data = $this->input->post();
               // $data['vendor_id'] = $vendor_id;
               // $data['vendor_id'] = 'sda700';
                $where = array('vendor_id' => $this->input->post('vendor_id'));
                if($this->backend->update_data($data, 'vendors', $where)) {
                  //echo $this->db->last_query(); exit;
                  $this->session->set_flashdata('vendor_add_msg', 'Vendor updated Successfully');
                  redirect('vendor');
                } else {
                  redirect('vendor');
                  $this->session->set_flashdata('vendor_add_msg', 'Error while adding Vendor...Try Again');
                }

            }
            else
            {
                        $where = array('vendor_id' => $vendor_id);
                        $vendor = $this->backend->get_data_by_cond('vendors', $where);
                      //  print_r($vendor); exit;
                        $this->load->view('includes/header_admin');
                        $this->load->view('includes/sidebar_admin');          
                        $this->load->view('vendor/add_vendor', array('vendor' => $vendor));
                        $this->load->view('includes/footer.php');
            }
        }

        function delete_vendor($vendor_id) {
          $this->load->model('backend');
           $where = array('vendor_id' => $vendor_id);
            if($this->backend->delete_data('vendors', $where)) {
                  //echo $this->db->last_query(); exit;
                  $this->session->set_flashdata('vendor_add_msg', 'Vendor deleted Successfully');
                  redirect('vendor');
                } else {
                  redirect('vendor');
                  $this->session->set_flashdata('vendor_add_msg', 'Error while adding Vendor...Try Again');
                }
        }

       
}
