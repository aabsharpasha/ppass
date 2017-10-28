<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Elasticsearch extends CI_Controller {
        function __construct() {
          parent::__construct();
          $this->userauth->authentication('normal');
        }
	public function index($arg=NULL)
	{
            require_once('ES/ElasticsearchHandler.php');
            
            $client = new ElasticsearchHandler(["https://search-data-search-hmw2uus67zyizjrdp6w4aoku54.us-east-1.es.amazonaws.com"]);
            
            print_r($client);

            //$view_data = array('listdata' => $data, 'pagination' => $links, 'offset' => $start);
            $this->load->view('frontend/elasticsearch');
	}
}
