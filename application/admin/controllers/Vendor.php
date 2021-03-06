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

function download_pdf_victor($id, $vendor_id) 
        {
                    $filename = "ppass_".time();
                    $pdfFilePath = FCPATH."downloads/reports/$filename.pdf";
                    $data['page_title'] = 'PPASS'; // pass data to the view

                    if (file_exists($pdfFilePath) == FALSE)
                    {
      
                        $where = array('checkin_details.checkin_id' => $id);
                         $this->db->select('checkin_details.checkin_id, checkin_details.vehicle_no, checkin_details.vehicle_model, checkout_details.duration_occupied, checkin_details.checkin_time, checkout_details.checkout_time, checkout_details.paid_amount, checkout_details.checkout_time, checkin_details.is_checkout')
                         ->from('checkin_details')
                         ->join('checkout_details', 'checkin_details.checkin_id = checkout_details.checkin_id', left)->where($where);
                        $result = $this->db->get();
                         // echo $this->db->last_query();
                          $res = $result->result();
                          if($res) {
                            $location = $this->get_vendor_details($vendor_id);
                            $pricing = $this->db->get_where('pricing_details', array('vendor_id' => $vendor_id))->row();
                            foreach($res as $line) {
                              $ret_str = $this->get_recipet_details_str($line, $location, $pricing, $vendor_id);
                              }
                          }
                          $css = $this->get_pdf_css();
                          $html = $css.$ret_str; // render the view into HTML
                    
                        $this->load->library('pdf');
                        $pdf = $this->pdf->load();
                        $pdf->SetFooter($_SERVER['HTTP_HOST'].'|PARKINGPASS|'.date("d-m-Y h:i A")); 
                        $pdf->SetDisplayMode('fullpage');
                        $pdf->WriteHTML($html); // write the HTML into the PDF
                        $pdf->debug = true;
                        $pdf->Output(); exit;
                     // $pdf->Output($pdfFilePath, 'F'); // save to file because we can

                    }

                    redirect("/downloads/reports/$filename.pdf");
         }

        function get_recipet_details_str($line, $location, $pricing, $vendor_id) 
        {
                  $ret_str =  '<div id="d-wrapper" style="width:400px;text-align:center;border:1px solid #1ba1e2;">
                                          <div class="zig-zag-bottom">
                                          </div>
                                          <div class="recpt">
                                           <div class="Order-logo"> <img src="'.base_url().'assets/img/logo.png" alt=""><h4>ParkingPass</h4></div>
                                              <div class="detail-rcpt">
                                                <div class="vendor-dtl">'.$location->vendor_name.' ('.$vendor_id.')</div>
                                                <div>'.$location->vendor_address.'</div>
                                                <br />
                                                <div>Auth. By: '.AUTHBY.'</div>';
                                                if($line->is_checkout) {
                                                  $ret_str .= '<div>GSTIN NO: '.GSTIN.'</div>';
                                                }
                                                $ret_str .= '<div></div>
                                                <div>Date: '.date("d-m-Y",strtotime($line->checkin_time)).', In Time: '.date("h:i A",strtotime($line->checkin_time)).'</div>';
                                                if($line->is_checkout) {
                                                    $ret_str .= '<div>Out: </span>  <span class="cols-3">'.date("h:i A",strtotime($line->checkin_time)).', Duration: '.$line->duration_occupied.'</div>';
                                                }
                                                $vehicle_details = ($line->vehicle_model ? '('.$line->vehicle_model.')' : $line->vehicle_no);
                                               $ret_str .= '<div><strong>Veh.: '.$vehicle_details.'</strong></div>';
                                              if($line->is_checkout) {
                                                  $ret_str .= '<div><strong>AMT PAID: Rs '.$line->paid_amount.'</strong><br />All fees and tax inclusive!</div>';
                                              } else {
                                                  if($line->vehicle_size == 1) {
                                                       $charges = "Rs ".$pricing->small_first_hr_rate." + ".$pricing->small_hourly_rate;
                                                  } else {
                                                       $charges = "Rs ".$pricing->big_first_hr_rate." + ".$pricing->big_hourly_rate; 
                                                  }
                                                   $ret_str .= '<div>Charges: '.$charges.' per hour</div>';
                                              }
                                                
                                              $ret_str .= '</br>
                                                <div>ppass-000'.$line->checkin_id.'</div>
                                                </br>';

                                              
                                                  if($line->is_checkout) {
                                                      $ret_str .= '<p>Parking at owner\'s risk.<br />Thanks for using e-bill sponsored by:<br />'.SPONSEREDBY.'</p>';
                                                  } else {
                                                        $ret_str .= '<p>Parking at owner\'s risk. Please check your vehicle number & report if incorrect.<br />Thanks for using e-bill sponsored by:<br />'.SPONSEREDBY.'</p>';
                                                  }
                                            $ret_str .= '</div>
                                              </div>
                                              <div class="zig-zag-top">
                                              </div>
                                              </div>';
              

                                              return $ret_str;

          }


        function get_pdf_css() {

          $css = '<style>
          .list-download{border-top: 1px solid;
padding: 5px;
margin: 5px;
list-style: none;

}
.selection {
  display: block !important;
}

#select2-vendors-container {
font-size: 15px !important;
}
.list-download div span {font-size: 17px !important;}
#result  {
    list-style: none !important;
    font-family: Courier;
    color: #0a0a0a;
    overflow: hidden;
  /*  border-bottom: 1px solid #a2a2a3;*/
    padding: 7px 0;
    line-height: 1.2;
}

#d-wrapper {
    background-color: #fff;
    margin-bottom: 2px;
    width: 414px;
    margin: auto;
}
#d-wrapper * {

margin:0;
padding:0;}
.center {text-align: center;}
#d-wrapper  div.sep {
    min-height: 200px;
    padding: 32px 0;

  }

#d-wrapper  .zig-zag-top:before{
    background-gradient:linear -45deg #1ba1e2 16px, red 16px blue 16px  transparent 0;
    background-gradient:linear 45deg #1ba1e2 16px  transparent 0;
        background-position: left top;
        background-repeat: repeat-x;
        background-size: 22px 32px;
        content: " ";
        display: block;

        height: 32px;
    width: 100%;

    position: relative;
    bottom: 64px;
    left:0;
  }

  .downlink {color: #fff;
font-style: underline;
padding: 15px !important;
float: right;
margin: 20px;
font:arial;}

#d-wrapper  div > * {
   /* margin: 0 19px;*/
    text-align:center;
  }

#d-wrapper  .zig-zag-bottom{
    margin: 32px 0;
    margin-top: 0;
    background: #1ba1e2;
  }

#d-wrapper  .zig-zag-top{
    margin: 32px 0;
    margin-bottom: 0;
      background: #1ba1e2;
  }

#d-wrapper  .zig-zag-bottom,
#d-wrapper  .zig-zag-top{
        padding: 32px 0;
  }

#d-wrapper  h1{
      font-size:2em;
      text-align:center;
      color:#fff;
      font-family:"PT Sans Narrow", "Fjalla One", sans-serif;
      font-weight:900;
      text-shadow:1px 1px 0 #1b90e2, 2px 2px 0 #1b90e2, 3px 3px 0 #1b90e2, 4px 4px 0 #1b90e2, 5px 5px 0 #1b90e2;

  }

#d-wrapper  div.sep p,
#d-wrapper  div.sep h1 {
    text-shadow:1px 1px 0 #888, 2px 2px 0 #888, 3px 3px 0 #888, 4px 4px 0 #888, 5px 5px 0 #888;
    color: #fff;
  }

#d-wrapper  h1{
     font-size:4em;
  }

#d-wrapper  .zig-zag-bottom:after{
    background-gradient: linear -45deg transparent 16px #1ba1e2 0; 
    background-gradient: linear 45deg transparent 16px #1ba1e2  0;
        background-repeat: repeat-x;
    background-position: left bottom;
        background-size: 22px 32px;
        content: "";
        display: block;

    width: 100%;
    height: 32px;

      position: relative;
    top:64px;
    left:0px;
  }

#d-wrapper  p{
    text-align: center;
  }

#d-wrapper  p:not(:last-child) {
    margin-bottom: 20px;
  }


p {
  text-align: center;
  
  
}
.auth{
  text-decoration: overline;
  color: #999;
  font-size: 2em;
}</style>';


return $css;
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
          $where = array('vendor_id' => $vendor_id, 'pin' => trim($vehicle_pin));
         $this->db->select('checkin_details.checkin_id, checkin_details.vehicle_no, checkin_details.vehicle_model, checkout_details.duration_occupied, checkin_details.checkin_time, checkout_details.checkout_time, checkout_details.paid_amount, checkout_details.checkout_time, checkin_details.is_checkout')
         ->from('checkin_details')
         ->join('checkout_details', 'checkin_details.checkin_id = checkout_details.checkin_id', left)->where($where);
         $this->db->where('(vehicle_model = "'.trim($vehicle_number).'" OR vehicle_no = "'.trim($vehicle_number).'") AND checkin_details.created_date >= DATE_SUB(NOW(), INTERVAL '.(REPORT_FETCH_HOURS).' HOUR)');
          $result = $this->db->get();
         // echo $this->db->last_query();
          $res = $result->result();
          if($res) {
            $location = $this->get_vendor_details($vendor_id);
            $pricing = $this->db->get_where('pricing_details', array('vendor_id' => $vendor_id))->row();
            foreach($res as $line) {
              
              $ret_str .='<div id="d-wrapper">
                            <div class="zig-zag-bottom">
                            </div>
                            <div class="recpt">
                             <div class="Order-logo"> <img src="'.base_url().'/assets/img/logo.png" alt=""><h4>ParkingPass</h4></div>
                                <div class="detail-rcpt">
                                  <div class="vendor-dtl">'.$location->vendor_name.' ('.$vendor_id.')</div>
                                  <div>'.$location->vendor_address.'</div>
                                  <br />
                                  <div>Auth. By: '.AUTHBY.'</div>';
                                  if($line->is_checkout) {
                                    $ret_str .= '<div>GSTIN NO: '.GSTIN.'</div>';
                                  }
                                  $ret_str .= '<div></div>
                                  <div>Date: '.date("d-m-Y",strtotime($line->checkin_time)).', In Time: '.date("h:i A",strtotime($line->checkin_time)).'</div>';
                                  if($line->is_checkout) {
                                      $ret_str .= '<div>Out: </span>  <span class="cols-3">'.date("h:i A",strtotime($line->checkin_time)).', Duration: '.$line->duration_occupied.'</div>';
                                  }
                                  $vehicle_details = ($line->vehicle_model ? '('.$line->vehicle_model.')' : $line->vehicle_no);
                                 $ret_str .= '<div><strong>Veh.: '.$vehicle_details.'</strong></div>';
                                if($line->is_checkout) {
                                    $ret_str .= '<div><strong>AMT PAID: Rs '.$line->paid_amount.'</strong><br />All fees and tax inclusive!</div>';
                                } else {
                                    if($line->vehicle_size == 1) {
                                         $charges = "Rs ".$pricing->small_first_hr_rate." + ".$pricing->small_hourly_rate;
                                    } else {
                                         $charges = "Rs ".$pricing->big_first_hr_rate." + ".$pricing->big_hourly_rate; 
                                    }
                                     $ret_str .= '<div>Charges: '.$charges.' per hour</div>';
                                }
                                  
                                $ret_str .= '</br>
                                  <div>ppass-000'.$line->checkin_id.'</div>
                                  </br>';

                                
                                  if($line->is_checkout) {
                                      $ret_str .= '<p>Parking at owner\'s risk.<br />Thanks for using e-bill sponsored by:<br />'.SPONSEREDBY.'</p>';
                                  } else {
                                        $ret_str .= '<p>Parking at owner\'s risk. Please check your vehicle number & report if incorrect.<br />Thanks for using e-bill sponsored by:<br />'.SPONSEREDBY.'</p>';
                                  }
                                $ret_str .= '</div>
                            </div>
                            <div class="zig-zag-top">
                              <a target="_blank" href="'.base_url().'vendor/download_pdf/'.$line->checkin_id.'/'.$vendor_id.'" class="downlink"><strong>Click here to Download</strong></a>
                            </div>
                   </div>';


             
            }
            // $ret_str .= '</ul>';
          } else {
            $ret_str = 'No Match Found.';
          }

          echo $ret_str; exit;
          
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
if($_REQUEST['debug']) {
$this->download_pdf_victor($id, $vendor_id); exit;
}
                    $filename = "ppass_".time();
                    $pdfFilePath = FCPATH."downloads/reports/$filename.pdf";
                    $data['page_title'] = 'PPASS'; // pass data to the view

                    if (file_exists($pdfFilePath) == FALSE)
                    {
      
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
            //echo $search; exit;
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
            $total_rows = $this->backend->get_row_count('pricing_details', $search);
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
