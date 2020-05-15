<?php if (!defined('BASEPATH')){ exit('No direct script access allowed'); }

/**
* 
* This controller contains the functions related to Drivers at the app end
* @author Casperon
*
* */

class Api extends DAPI_Controller {
    function __construct() {
        parent::__construct();
        $this->load->helper(array('date','email'));
        $this->load->model(array('api_model'));
		$headers = $this->input->request_headers();
		header('Content-type:application/json;charset=utf-8');
		
    }
	public function index() {
		echo 'API - Version 1.0';
	}
	public function create_vehicle() {
		$returnArr['status'] = '0';
		$returnArr['response'] = '';
		try {
			$vehicle_number = $this->input->post('vehicle_number');  
			$maximum_capacity = $this->input->post('maximum_capacity'); 
			$maker = $this->input->post('maker'); 
			$model = $this->input->post('model'); 
			$year_of_vehicle = $this->input->post('year_of_model'); 
			$owner_name = $this->input->post('owner_name'); 
			$owner_contact = $this->input->post('owner_contact'); 
			$lat = $this->input->post('lat'); 
			$lon = $this->input->post('lon'); 
			
			if ($vehicle_number != "" && $maximum_capacity!='' && $maker!='' && $model!='' && $year_of_vehicle!='' && $owner_name!='' && $owner_contact!='' && $lat!='' && $lon!='') {
				$checkVehicle = $this->api_model->get_selected_fields(VEHICLE,array('vehicle_number' =>(string)$vehicle_number),array("_id","status"));
				if($checkVehicle->num_rows()==0){
					$vehicle_data=array(
						'vehicle_number' =>(string)$vehicle_number,
						'maximum_capacity' =>floatval($maximum_capacity),
						'maker' =>(string)$maker,
						'model' =>(string)$model,
						'year_of_model' =>floatval($year_of_vehicle),
						'owner_name' =>$owner_name,
						'owner_contact' =>$owner_contact,
						'loc'=>array('lon'=>floatval($lon),
									 'lat'=>floatval($lat)
									),
						'created_at'=>MongoDATE(time()),
					  );
					$this->api_model->simple_insert(VEHICLE,$vehicle_data);
					$vehicle_id = $this->mongo_db->insert_id();
					$returnArr['status'] = '1';
					$returnArr['response'] = array('vehicle_id'=>$vehicle_id,
												'message'=>'Vehicle Created Successfully');
				} else {
					$returnArr['response'] = 'Already this vehicle number exists';
				}
			} else {
				$returnArr['response'] = 'some parameters are missing';
			}
		} catch (MongoException $ex) {
			$returnArr['response'] = 'something went wrong Please try again';
		}
		$json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
		echo $this->cleanString($json_encode);
	}
	public function create_consignment() {
		$returnArr['status'] = '0';
		$returnArr['response'] = '';
		try {
			$material_code = $this->input->post('material_code');  
			$weight = $this->input->post('weight'); 
			$shipper_name = $this->input->post('shipper_name'); 
			$shipper_contact = $this->input->post('shipper_contact'); 
			$lat = $this->input->post('lat'); 
			$lon = $this->input->post('lon'); 
			if ($material_code != "" && $weight!='' && $shipper_name!='' && $shipper_contact!=''  && $lat!='' && $lon!='') {
					$consigment_number=$this->api_model->get_consigment_id();
					$checkShipper = $this->api_model->get_selected_fields(SHIPPER,array('shipper_contact' =>(string)$shipper_contact),array("_id"));
					if($checkShipper->num_rows()==0) {
						$shipper_data=array(
						'shipper_name' =>(string)$shipper_name,
						'shipper_contact' =>(string)$shipper_contact,
						'created_at'=>MongoDATE(time()),
						);
					   $this->api_model->simple_insert(SHIPPER,$shipper_data);
					   $shipper_id = $this->mongo_db->insert_id();
					} else {
						$shipper_id=(string)$checkShipper->row()->_id;
					}
					$consigment_data=array(
						'consigment_number' =>floatval($consigment_number),
						'material_code' =>(string)$material_code,
						'weight' =>floatval($weight),
						'shipper_id'=>MongoID($shipper_id),
						'shipper_name' =>(string)$shipper_name,
						'shipper_contact' =>(string)$shipper_contact,
						'loc'=>array('lon'=>floatval($lon),
									 'lat'=>floatval($lat)
									),
						'created_at'=>MongoDATE(time()),
					);
					$this->api_model->simple_insert(CONSIGNMENT,$consigment_data);
					$consignment_id = $this->mongo_db->insert_id();
					$returnArr['status'] = '1';
					$returnArr['response'] = array('consigment_ref'=>$consigment_number,
												   'consignment_id'=>$consignment_id,
												'message'=>'Consignment Created Successfully');
			} else {
				$returnArr['response'] = 'some parameters are missing';
			}
		} catch (MongoException $ex) {
			$returnArr['response'] = 'something went wrong Please try again';
		}
		$json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
		echo $this->cleanString($json_encode);
	}
	public function create_trip() {
		$returnArr['status'] = '0';
		$returnArr['response'] = '';
		try {
			$vehicle_id = $this->input->post('vehicle_id');  
			$consignment_id = $this->input->post('consignment_id'); 
			$start_date = $this->input->post('start_date'); 
			$destination_lat = $this->input->post('destination_lat'); 
			$destination_lon = $this->input->post('destination_lon'); 
			$current_lat = $this->input->post('current_lat'); 
			$current_lon = $this->input->post('current_lon'); 
			if ($vehicle_id != "" && $consignment_id!='' && $start_date!='' && $destination_lat!=''  && $destination_lon!='' && $current_lat!='' && $current_lon!='') {
					$trip_id=$this->api_model->get_trip_id();
					$get_vehicle_info = $this->api_model->get_all_details(VEHICLE,array('_id' =>MongoID($vehicle_id)));
					if($get_vehicle_info->num_rows() >0){
						$get_consigment_info = $this->api_model->get_all_details(CONSIGNMENT,array('_id' =>MongoID($consignment_id)));
						if($get_consigment_info->num_rows() >0){
							$trip_data=array(
								'trip_id' =>floatval($trip_id),
								'vehicle'=>array(
									'id'=>MongoID($vehicle_id),
									'vehicle_number'=>$get_vehicle_info->row()->vehicle_number,
									'vehicle_maker'=>$get_vehicle_info->row()->maker,
									'vehicle_model'=>$get_vehicle_info->row()->model,
									'maximum_capacity'=>$get_vehicle_info->row()->maximum_capacity,
									'owner_name'=>$get_vehicle_info->row()->owner_name,
									'owner_contact'=>$get_vehicle_info->row()->owner_contact,
								
								  ),
								'consigment'=>array(
									'id'=>MongoID($consignment_id),
									'consigment_number'=>$get_consigment_info->row()->consigment_number,
									'material_code'=>$get_consigment_info->row()->material_code,
									'weight'=>$get_consigment_info->row()->weight,
									'shipper_id'=>MongoID($get_consigment_info->row()->shipper_id),
									'shipper_name'=>$get_consigment_info->row()->shipper_name,
									'shipper_contact'=>$get_consigment_info->row()->shipper_contact,
							     ),
								'destination_loc'=>array('lon'=>floatval($destination_lon),
											 'lat'=>floatval($destination_lat)
											),
								'current_loc'=>array('lon'=>floatval($current_lat),
											 'lat'=>floatval($current_lon)
											),
								 'booked_date'=>MongoDATE(time()),
								 'start_date'=>MongoDATE(strtotime($start_date))
							);
							$this->api_model->simple_insert(TRIP,$trip_data);
							$returnArr['status'] = '1';
							$returnArr['response'] = array('trip_id'=>$trip_id,
														'message'=>'Trip Booked Successfully');
						} else {
							$returnArr['response'] = 'No consignment record Found';
						}
					} else {
						$returnArr['response'] = 'No vehicle Information Found';
					}
					
			} else {
				$returnArr['response'] = 'some parameters are missing';
			}
		} catch (MongoException $ex) {
			$returnArr['response'] = 'something went wrong Please try again';
		}
		$json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
		echo $this->cleanString($json_encode);
	}
	public function update_trip_location() {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
		try {
			$trip_id = $this->input->post('trip_id');
			$vehicle_id = $this->input->post('vehicle_id');
			$lat = $this->input->post('lat');
			$lon = $this->input->post('lon');
			if($trip_id != '' && $lat != '' && $lon != '' && $vehicle_id!=''){
				$getTrip = $this->api_model->get_selected_fields(TRIP, array('trip_id' =>floatval($trip_id)), array('_id'));
				if($getTrip->num_rows()==1){
					$dataArr =  array('loc' =>array('lon'=>floatval($lon),
											 'lat'=>floatval($lat)
											),
									   'updated_time'=>MongoDATE(time()),
									   'trip_id'=>floatval($trip_id),
									   'vehicle_id'=>MongoID($vehicle_id)
									 );
					$this->api_model->simple_insert(TRIP_TRACKING,$dataArr);
					$returnArr['status'] = '1';
					$returnArr['response'] = 'Location Updated successfully';
				}else{
					$returnArr['response'] = 'No Trip Found';
				}
            } else {
                $returnArr['response'] = 'Some Parameters are missing';
            }
		
		} catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string('Error in connection','error_in_connection');
        }
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }
		
}
