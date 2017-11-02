<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Vendor extends CI_Controller {
        function __construct() {
          parent::__construct();
          $this->userauth->authentication('normal');
        }
  public function index($arg=NULL)
  {
            $this->load->view('includes/header_admin');
            $this->load->view('includes/sidebar_admin');          
            $this->load->view('vendor/list');
            $this->load->view('includes/footer.php');

  }
        
        function get_excel_uploaded_data($limit, $start){
            return $this->backend->get_data('excel_data',$limit,$start);           
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
