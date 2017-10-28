<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Listing extends CI_Controller {
        function __construct() {
          parent::__construct();
          $this->userauth->authentication('normal');
        }
	public function index($arg=NULL)
	{
            
            /*$start = (!empty($arg)) ? $arg : 0;           
            $limit = 0;
            $this->load->library('pagination');
            $total_rows = $this->backend->get_row_count('excel_data');
            $data = $this->get_excel_uploaded_data($limit, $start);
            
            
            $config['base_url'] = base_url('listing/');
            $config['total_rows'] = $total_rows;
            $config['per_page'] = $limit;
            
            // custom paging configuration
             
            $config['full_tag_open'] = '<ul class="pagination">';
            $config['full_tag_close'] = '</u>';
             
            $config['first_link'] = 'First Page';
            $config['first_tag_open'] = '<li class="firstlink page-item">';
            $config['first_tag_close'] = '</li>';
             
            $config['last_link'] = 'Last Page';
            $config['last_tag_open'] = '<li class="lastlink page-item">';
            $config['last_tag_close'] = '</li>';
             
            $config['next_link'] = 'Next Page';
            $config['next_tag_open'] = '<li class="nextlink page-item">';
            $config['next_tag_close'] = '</li>';
 
            $config['prev_link'] = 'Prev Page';
            $config['prev_tag_open'] = '<li class="prevlink page-item">';
            $config['prev_tag_close'] = '</li>';
 
            $config['cur_tag_open'] = '<li class="curlink page-item active"><a class="page-link" href="javascript:void(0);">';
            $config['cur_tag_close'] = '</a></li>';
 
            $config['num_tag_open'] = '<li class="numlink page-item">';
            $config['num_tag_close'] = '</li>';
            
            $config['attributes'] = array('class' => 'page-link');
            
            $this->pagination->initialize($config);
            $links = $this->pagination->create_links();
            $view_data = array('listdata' => $data, 'pagination' => $links, 'offset' => $start);*/
            $this->load->view('frontend/list');
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
            $total_rows = $this->backend->get_row_count('excel_data', $search);
            
            $res = array();
            $result = $this->backend->get_data('excel_data', $limit, $start, $search);
            $i = 0; $j = $start+1;
            if(!empty($result)){
              foreach($result as $row){
                $res[$i][] = $j;
                $res[$i][] = $row->title;
                $res[$i][] = $row->audio_url;
                $res[$i][] = 'asasasdasd'.$i;
                $i++; $j++;
              }
            }
            //echo '<pre>'; print_r($res); exit;
            $data = array('draw' => $draw, 'recordsTotal' => $total_rows, 'recordsFiltered' => $total_rows, 'data' => $res);
            echo json_encode($data); exit;
        }
}
