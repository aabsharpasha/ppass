<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Backend extends CI_Model {
  function __construct() {
          parent::__construct();
        }
  function insert_data($data,$table){
    try {
      $this->db->insert($table, $data);

      return true;
    } catch(exception $e) {
     
      return false;
    }
  }

  function update_data($data, $table, $where) {
    try {
      if($this->db->update($table, $data, $where)) {
        return true;
      } else 
      return false;

      
    } catch(exception $e) {
     
      return false;
    }
  }

   function delete_data($table, $where) {
    try {
      if($this->db->delete($table, $where)) {
        return true;
      } else 
      return false;

      
    } catch(exception $e) {
     
      return false;
    }
  }
  
  function get_data($table,$limit=NULL,$start = NULL, $search=NULL, $where = array()){
      $this->db->select('*');
      $this->db->from($table);
     if($where)
      $this->db->where($where);
      if(!empty($search)) {
        if($table == 'users') {
           $this->db->like('user_name', $search);
        } else if($table == 'vendor'){
         $this->db->like('vendor_name', $search);
         $this->db->or_like('vendor_address', $search);
       }
      }
      if(!empty($limit)){
        $this->db->limit($limit, $start);
      }
      $query = $this->db->get();
      $num = $query->num_rows();
      if($num > 0){
        return $query->result();
      } else {
        return '';
      }
  }
  
  function get_row_count($table, $search=NULL, $cond = '') {
      $this->db->select('*');
      $this->db->from($table);
      if($cond)
      $this->db->where($cond);
      if(!empty($search)) {
        if($table == 'users') {
           $this->db->like('user_name', $search);
        } else if($table == 'vendor') {
         $this->db->like('vendor_name', $search);
         $this->db->or_like('vendor_address', $search);
       }
      }
      $query = $this->db->get();
      $num = $query->num_rows();
      return $num;
  }

  function get_data_by_cond($table, $where){
      $this->db->select('*');
      $this->db->from($table);
      $this->db->where($where);
      $query = $this->db->get();
      $num = $query->num_rows();
      if($num > 0){
        return $query->row();
      } else {
        return '';
      }
  }
}