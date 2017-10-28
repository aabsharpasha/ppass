<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {
        function __construct() {
          parent::__construct();
          $this->userauth->authentication('admin');
        }
	public function index()
	{
            
	}
        
        public function excel_upload(){
            $this->load->view('backend/excel_upload');      
        }
        
        public function upload_file(){  
          //require_once('Excel/reader.php');
          require_once('PHPExcel/IOFactory.php');
          //echo '<pre>'; print_r($_FILES);   
          $file_data = $_FILES;
          $uploaddir = FCPATH.'uploads/';
          $filename = str_replace(' ', '_', basename($_FILES['excelfile']['name']));
          $file = $uploaddir . $filename;
          $ext = pathinfo($file, PATHINFO_EXTENSION);
          
          $ext_arr = array('xls','xlsx');
          if(in_array($ext, $ext_arr)) {
            if (move_uploaded_file($_FILES['excelfile']['tmp_name'], $file)) { 
              chmod($file, 0777);            
//              $data = new Spreadsheet_Excel_Reader();
//              $data->read($file);
              $objPHPExcel = PHPExcel_IOFactory::load($file);
              $sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
              $filedata = array_map('array_filter', $sheetData);
              //echo '<pre>'; print_r($filedata); exit;
              //$filedata = $data->sheets[0]['cells'];
              array_shift($filedata);
              $this->import_excel_data($filedata);
              unlink($file);
              $res['message'] = 'File successfully uploaded.';
              $res['status'] = 1;
            } else {   
              $res['message'] = "File is not uploaded."; 
              $res['status'] = 0;
            }
          } else {
             $res['message'] = 'The file type you have uploaded is not allowed. Please upload .xls or .xlsx file.';
             $res['status'] = 0;
          }
          $this->session->set_flashdata('msg',$res);
          redirect(base_url('admin/excel_upload'));
        }
        
   function import_excel_data($filedata=array()){
     $data = array();
     foreach($filedata as $row){
       $data = array(
        'title' => $row['A'],
        'audio_url' => $row['B'],
       );
       $this->backend->insert_data($data,'excel_data');
     }   
   }
   
}
