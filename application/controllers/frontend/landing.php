<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');


class Landing extends DAPI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->helper(array('cookie', 'date', 'form', 'email'));
        $this->load->library(array('encrypt', 'form_validation'));
        $this->load->model('api_model');
        $returnArr = array();
        
    }
	
    public function index() {  
		$this->data['vehicle_list']=$this->api_model->get_all_details(VEHICLE,array(),array("created_at"=>1));
        $this->load->view('vehicle/vehicle_list', $this->data);
    }
	
	public function shipper_list() {  
		$this->data['shipper_list']=$this->api_model->get_all_details(SHIPPER,array(),array("created_at"=>1));
        $this->load->view('shipper/shipper_list', $this->data);
    }
	public function trip_list() {
		$filter = "";
		$filter_condition=array();
		if (isset($_GET['date_range']) && $_GET['date_range']!='') {
			 $date_range = $this->input->get('date_range');
			 $dateArr=explode('-',$date_range);
			 $date_from=strtotime($dateArr[0]);
			 $date_to=strtotime($dateArr[1]);
			 $filter = "filter";
             $filter_condition['start_date']  =  array('$gte' =>MongoDATE($date_from),'$lte' =>MongoDATE($date_to));
		}
		$this->data['filter'] = $filter;
		$this->data['trip_list']=$tripList=$this->api_model->get_all_details(TRIP,$filter_condition,array("booked_date"=>1));
		if(isset($_GET['export']) && $_GET['export'] == 'excel'){
			$this->load->helper('export_helper');
			export_trip_list($tripList);
        }
        $this->load->view('trip/trip_list', $this->data);
    }
	
  }

