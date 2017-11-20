<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Backend extends CI_Model {
  function __construct() {
          parent::__construct();
        }
  function insert_data($data,$table){
     $this->db->insert($table, $data);

     $insert_id = $this->db->insert_id();
     return $insert_id ? $insert_id : $this->db->affected_rows();
  }
  

  function update_data($data, $table, $where) {
    try {
      if($this->db->update($table, $data, $where)) {
        return true;
      } else {
        return false;
      }
      
    } catch(exception $e) {
     
      return false;
    }
  }

  function get_data($table,$limit=NULL,$start, $search=NULL){
      $this->db->select('*');
      $this->db->from($table);
      if(!empty($search)){
         $this->db->like('title', $search);
         $this->db->or_like('audio_url', $search);
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
  
  function get_row_count($table, $search=NULL){
      $this->db->select('*');
      $this->db->from($table);
      if(!empty($search)){
         $this->db->like('title', $search);
         $this->db->or_like('audio_url', $search);
      }
      $query = $this->db->get();
      $num = $query->num_rows();
      return $num;
  }
}