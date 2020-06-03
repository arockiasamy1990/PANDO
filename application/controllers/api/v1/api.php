<?php if (!defined('BASEPATH')){ exit('No direct script access allowed'); }



class Api extends DAPI_Controller {
    function __construct() {
        parent::__construct();
        $this->load->helper(array('date','email'));
        $this->load->model(array('api_model'));
		$headers = $this->input->request_headers();
		header('Content-type:application/json;charset=utf-8');
		header('Access-Control-Allow-Origin: *');  
		header('Access-Control-Allow-Headers: *');  
		
       
		
    }
	public function index() {
		echo 'API - Version 1.0';
	}
	public function create_vehicle() {
		$returnArr['status_code'] = '';
		$returnArr['response'] = '';
		try {
			$data = json_decode(file_get_contents('php://input'), true);
			if(!empty($data)) {
				 $vehicle_number = $data['vehicle_number'];  
				 $maximum_capacity = $data['maximum_capacity']; 
				 $maker = $data['maker']; 
				 $model = $data['model']; 
				 $year_of_vehicle = $data['year_of_model']; 
				 $owner_name = $data['owner_name']; 
				 $owner_contact = $data['owner_contact']; 
				 $lat = $data['lat']; 
				 $lon = $data['lon']; 
				
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
					$returnArr['status_code'] = http_response_code(200);
					$returnArr['response'] = array('vehicle_id'=>$vehicle_id,
												'message'=>'Vehicle Created Successfully');
				} else {
					http_response_code(400);
					$returnArr['status_code'] = "400";
					$returnArr['response'] = 'Already this vehicle number exists';
				}
			} else {
				http_response_code(400);
				$returnArr['status_code'] = "400"; 
				$returnArr['response'] = 'some parameters are missing';
			}
		  } else {
			 http_response_code(400);
			 $returnArr['status_code'] = "400";
			 $returnArr['response'] = 'something went wrong Please try again';
		 }
		} catch (MongoException $ex) {
			http_response_code(500);
			$returnArr['status_code'] = "500";
			$returnArr['response'] = 'something went wrong Please try again';
		}
		$json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
		echo $this->cleanString($json_encode);
	}
	
	public function auth_user() {
		$returnArr['status_code'] = '';
		$returnArr['response'] = '';
		try {
			$data = json_decode(file_get_contents('php://input'), true);
			
			if(!empty($data)) {
				$username=$data['authData']['name'];
				$email=$data['authData']['email'];
				$password=$data['authData']['password'];
				$mode=$data['authData']['mode'];
			    if ($username != "" && $email!='' && $password!='' && $mode!='') {
					if($mode=='signup'){
					   $checkUser=$this->api_model->get_selected_fields(USERS,array('email'=>$email),array("_id"));
					   if($checkUser->num_rows()==0) {
							  $DataArr=array(
								'user_name' =>(string)$username,
								'email' =>(string)$email,
								'password'=>md5($password),
								'created_at'=>MongoDATE(time())
							);
							$this->api_model->simple_insert(USERS,$DataArr);
							$user_id = $this->mongo_db->insert_id(); 
							$returnArr['status_code'] = http_response_code(200);
							$returnArr['response'] = array('user_id'=>$user_id,
														   'expiration'=>time()+1800
														   );
					   } else{
						   http_response_code(400);
						   $returnArr['status_code'] = "400";
						   $returnArr['response'] = "Email already exists";
					   }
					} else {
						$checkUser=$this->api_model->get_selected_fields(USERS,array('email'=>$email,"password"=>md5($password)),array("_id"));
					    if($checkUser->num_rows()==1) {
							$returnArr['status_code'] = http_response_code(200);
							$returnArr['response'] = array('user_id'=>(string)$checkUser->row()->_id,
														   'expiration'=>time()+1800
														   );
						} else {
						   http_response_code(400);
						   $returnArr['status_code'] = "400";
						   $returnArr['response'] = "email or password is incorrect";
						}
					}
			    } else {
					http_response_code(400);
					$returnArr['status_code'] = "400";
					$returnArr['response'] = "Some Parameters are Missing";
			    }
			}	
		} catch (MongoException $ex) {
			http_response_code(500);
			$returnArr['status_code'] = "500";
			$returnArr['response'] = 'something went wrong Please try again';
		}
		$json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
		echo $this->cleanString($json_encode);
	}
	public function list_vehicle() {
		$returnArr['status_code'] = '';
		$returnArr['response'] = '';
		try {
			
			$vehicle_list=$this->api_model->get_all_details(VEHICLE,array(),array("created_at"=>1));
			$vehicleArr=array();
			if($vehicle_list->num_rows() >0) {
				$i=1;
				foreach($vehicle_list->result() as $data) {
					$vehicleArr[]=array(
									'sno'=>$i,
									'vehicle_number'=>$data->vehicle_number,
									'maker'=>$data->maker,
									'model'=>$data->model,
									'maximum_capacity'=>$data->maximum_capacity,
									'owner_name'=>$data->owner_name,
									'owner_contact'=>$data->owner_contact,
									'created_at'=>date('d-m-Y h:i a',MongoEPOCH($data->created_at))
					
					);
					$i++;
				}
				$returnArr['status_code'] = http_response_code(200);
				$returnArr['response'] = array('vehicle_list'=>$vehicleArr);
			}
		} catch (MongoException $ex) {
			http_response_code(500);
			$returnArr['status_code'] = "500";
			$returnArr['response'] = 'something went wrong Please try again';
		}
		$json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
		echo $this->cleanString($json_encode);
	}
	public function list_shipper() {
		$returnArr['status_code'] = '';
		$returnArr['response'] = '';
		try {
			$shipper_list=$this->api_model->get_all_details(SHIPPER,array(),array("created_at"=>1));
			$shipperArr=array();
			if($shipper_list->num_rows() >0) {
				$i=1;
				foreach($shipper_list->result() as $data) {
					$shipperArr[]=array(
									'sno'=>$i,
									'shipper_name'=>$data->shipper_name,
									'shipper_contact'=>$data->shipper_contact,
									'created_at'=>date('d-m-Y h:i a',MongoEPOCH($data->created_at))
					
					);
					$i++;
				}
				$returnArr['status_code'] = http_response_code(200);
				$returnArr['response'] = array('shipper_list'=>$shipperArr);
			}
		} catch (MongoException $ex) {
			http_response_code(500);
			$returnArr['status_code'] = "500";
			$returnArr['response'] = 'something went wrong Please try again';
		}
		$json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
		echo $this->cleanString($json_encode);
	}
	public function list_trip() {
		$returnArr['status_code'] = '';
		$returnArr['response'] = '';
		try {
			$filter_condition=array();
			$data = json_decode(file_get_contents('php://input'), true);
			if(!empty($data)) {
				 $date_range = $data['searchdata']['search'];
				 $dateArr=explode('-',$date_range);
				 $date_from=strtotime($dateArr[0]);
				 $date_to=strtotime($dateArr[1]);
				 $filter_condition['start_date']  =  array('$gte' =>MongoDATE($date_from),'$lte' =>MongoDATE($date_to));
			}
			$trip_list=$this->api_model->get_all_details(TRIP,$filter_condition,array("booked_date"=>1));
			$tripArr=array();
			if($trip_list->num_rows() >0) {
				$i=1;
				
				foreach($trip_list->result() as $data) {
					$tripArr[]=array(
						'sno'=>$i,
						'trip_id'=>$data->trip_id,
						'vehicle_number'=>$data->vehicle['vehicle_number'],
						'owner_name'=>$data->vehicle['owner_name'],
						'consigment_number'=>$data->consigment['consigment_number'],
						'material_code'=>$data->consigment['material_code'],
						'weight'=>$data->consigment['weight'],
						'shipper_name'=>$data->consigment['shipper_name'],
						'start_date'=>date('d-m-Y h:i a',MongoEPOCH($data->start_date)),
						'booked_date'=>date('d-m-Y h:i a',MongoEPOCH($data->booked_date))
					);
					$i++;
				}
				
			}
			$returnArr['status_code'] = http_response_code(200);
			$returnArr['response'] = array('trip_list'=>$tripArr);
		} catch (MongoException $ex) {
			http_response_code(500);
			$returnArr['status_code'] = "500";
			$returnArr['response'] = 'something went wrong Please try again';
		}
		$json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
		echo $this->cleanString($json_encode);
	}
	public function create_consignment() {
		$returnArr['status_code'] = '';
		$returnArr['response'] = '';
		try {
			$data = json_decode(file_get_contents('php://input'), true);
			if(!empty($data)) {
				$material_code = $data['material_code'];  
				$weight = $data['weight']; 
				$shipper_name = $data['shipper_name']; 
				$shipper_contact = $data['shipper_contact']; 
				$lat = $data['lat']; 
				$lon = $data['lon']; 
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
					$returnArr['status_code'] = http_response_code(200);
					$returnArr['response'] = array('consigment_ref'=>$consigment_number,
												   'consignment_id'=>$consignment_id,
												'message'=>'Consignment Created Successfully');
			} else {
				http_response_code(400);
			    $returnArr['status_code'] = "400";
				$returnArr['response'] = 'some parameters are missing';
			}
		 } else {
			 http_response_code(400);
			 $returnArr['status_code'] = "400";
			 $returnArr['response'] = 'something went wrong Please try again';
		 }
		} catch (MongoException $ex) {
			http_response_code(500);
			$returnArr['status_code'] = "500";
			$returnArr['response'] = 'something went wrong Please try again';
		}
		$json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
		echo $this->cleanString($json_encode);
	}
	public function create_trip() {
		$returnArr['status_code'] = '0';
		$returnArr['response'] = '';
		try {
			$data = json_decode(file_get_contents('php://input'), true);
			if(!empty($data)) {
			$vehicle_id = $data['vehicle_id'];  
			$consignment_id = $data['consignment_id']; 
			$start_date = $data['start_date']; 
			$destination_lat = $data['destination_lat']; 
			$destination_lon = $data['destination_lon']; 
			$current_lat = $data['current_lat']; 
			$current_lon = $data['current_lon']; 
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
							$returnArr['status_code'] = http_response_code(200);
							$returnArr['response'] = array('trip_id'=>$trip_id,
														'message'=>'Trip Booked Successfully');
						} else {
							http_response_code(400);
							$returnArr['status_code'] = "400";
							$returnArr['response'] = 'No consignment record Found';
						}
					} else {
						http_response_code(400);
						$returnArr['status_code'] = "400";
						$returnArr['response'] = 'No vehicle Information Found';
					}
					
			} else {
				http_response_code(400);
				$returnArr['status_code'] = "400";
				$returnArr['response'] = 'some parameters are missing';
			}
		 } else {
			 http_response_code(400);
			 $returnArr['status_code'] = "400";
			 $returnArr['response'] = 'something went wrong Please try again';
		 }
	   } catch (MongoException $ex) {
			
			http_response_code(500);
			$returnArr['status_code'] = "500";
			$returnArr['response'] = 'something went wrong Please try again';
		}
		$json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
		echo $this->cleanString($json_encode);
	}
	public function update_trip_location() {
        $returnArr['status_code'] = '';
        $returnArr['response'] = '';
		try {
			$data = json_decode(file_get_contents('php://input'), true);
			if(!empty($data)) {
				$trip_id = $data['trip_id'];
				$vehicle_id = $data['vehicle_id'];
				$lat = $data['lat'];
				$lon = $data['lon'];
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
						$returnArr['status_code'] = http_response_code(200);
						$returnArr['response'] = 'Location Updated successfully';
					}else{
						http_response_code(404);
						$returnArr['status_code'] = "404";
						$returnArr['response'] = 'No Trip Found';
					}
				} else {
					 http_response_code(400);
					 $returnArr['status_code'] = "400";
					$returnArr['response'] = 'Some Parameters are missing';
				}
		 } else {
			 http_response_code(400);
			 $returnArr['status_code'] = "400";
			 $returnArr['response'] = 'something Went wrong';
		 }
		} catch (MongoException $ex) {
			 http_response_code(500);
			 $returnArr['status_code'] = "500";
            $returnArr['response'] = 'something Went wrong';
        }
        $json_encode = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }
		
}
