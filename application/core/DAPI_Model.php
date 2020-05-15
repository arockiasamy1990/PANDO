<?php if (!defined('BASEPATH')){ exit('No direct script access allowed'); }


class DAPI_Model extends CI_Model {

    /**
    * 
    * This function connect the database and load the functions from CI_Model
    *
    * */
    public function __construct() {
        parent::__construct();
    }

    /**
    *
    * This functions returns all the collection details using @param 
    * @param String $collection
    * @param Array $sortArr
    * @param Array $condition
    * @param Numeric $limit
    * @param Numeric $offset
    * @param Array $likearr
    *
    * */
    public function get_all_details($collection, $condition = array(), $sortArr = array(), $limit = FALSE, $offset = FALSE, $likearr = array()) {
        $this->mongo_db->select();
        if (!empty($condition)) {
            $this->mongo_db->where($condition);
        }
        if (!empty($likearr)) {
            if (count($likearr) > 0) {
                foreach ($likearr as $key => $val) {
                    $this->mongo_db->or_like($key, $val);
                }
            } else {
                $this->mongo_db->like($key, $val);
            }
        }
        if ($sortArr != '' && is_array($sortArr) && !empty($sortArr)) {
            $this->mongo_db->order_by($sortArr);
        }
        if ($limit !== FALSE && is_numeric($limit) && $offset !== FALSE && is_numeric($offset)) {
            $this->mongo_db->limit($limit);
            $this->mongo_db->offset($offset);
        } 
        $res = $this->mongo_db->get($collection);
        return $res;
    }

    /**
     *
     * This functions returns all the collection details using @param 
     * @param String $collection
     * @param Array $sortArr
     * @param Array $fields
     * @param Array $condition
     * @param Numeric $limit
     * @param Numeric $offset
     * @param Array $likearr
     *
     * */
    public function get_selected_fields($collection, $condition = array(), $fields = array(), $sortArr = array(), $limit = FALSE, $offset = FALSE, $likearr = array()) {
        $this->mongo_db->select($fields);
        if (!empty($condition)) {
            $this->mongo_db->where($condition);
        }
        if (!empty($likearr)) {
            if (count($likearr) > 0) {
                foreach ($likearr as $key => $val) {
                    $this->mongo_db->or_like($key, $val);
                }
            } else {
                $this->mongo_db->like($key, $val);
            }
        }
        if ($sortArr != '' && is_array($sortArr) && !empty($sortArr)) {
            $this->mongo_db->order_by($sortArr);
        }
        if ($limit !== FALSE && is_numeric($limit) && $offset !== FALSE && is_numeric($offset)) {
            $this->mongo_db->limit($limit);
            $this->mongo_db->offset($offset);
        } 
        $res = $this->mongo_db->get($collection);
        return $res;
    }

    /**
     * 
     * This function do all insert and edit operations
     * @param String $collection	   -->	Collection name
     * @param String $mode		   -->	Insert, Update
     * @param Array $excludeArr	   -->   To avoid post inputs
     * @param Array $dataArr         -->   Add additional inputs with posted inputs
     * @param Array $condition      -->  Applicable only for updates
     *
     * */
    public function commonInsertUpdate($collection = '', $mode = '', $excludeArr = '', $dataArr = '', $condition = '') {
        $inputArr = array();
        foreach ($this->input->post() as $key => $val) {
            if (!in_array($key, $excludeArr)) {
                if (is_numeric($val)) {
                    $inputArr[$key] = floatval($val);
                } else {
                    $inputArr[$key] = $val;
                }
            }
        }
        $finalArr = array_merge($inputArr, $dataArr);
				
        if ($mode == 'insert') {
            return $this->mongo_db->insert($collection, $finalArr);
        } else if ($mode == 'update') {
            $this->mongo_db->where($condition);
            $this->mongo_db->set($finalArr);
            return $this->mongo_db->update($collection);
        }
    }

    /**
     * 
     * Simple function for inserting data into a collection
     * @param String $collection
     * @param Array $data
     *
     * */
    public function simple_insert($collection = '', $data = '') {
        return $this->mongo_db->insert($collection, $data);
    }

	/**
	*
	* This functions updates the collection details using @param 
	* @param String $collection
	* @param Array $data
	* @param Array $condition
	*
	* */
    public function update_details($collection = '', $data = '', $condition = '',$options = array()) {
        if (!empty($collection)) {
            $this->mongo_db->where($condition);
            $this->mongo_db->set($data);
            return $this->mongo_db->update_all($collection,array(),$options);
        }
    }
    
    public function findandmodify($collection = '', $data = '', $condition = '',$fields = array(), $sortArr = array()) {
        if (!empty($collection)) {
        
            return $this->mongo_db->findAndModify($collection,$condition,array(),$data,$fields);
            
        }
    }
    
    /**
     * 
     * This function deletes the document based upon the condition
     * @param String $collection
     * @param Array $condition
     * */
    public function commonDelete($collection = '', $condition = '') {
        $this->mongo_db->where($condition);
        return $this->mongo_db->delete_all($collection);
    }

    /**
     *
     * Common function for executing mongoDB query
     * @param String $Query	->	mongoDB Query
     *
     * */
    public function ExecuteQuery($Query) {
        $res = $this->mongo_db->command($Query);
        return $res;
    }

    /**
     *
     * Common function for get last inserted _id
     *
     * */
    public function get_last_insert_id() {
        $last_insert_id = $this->mongo_db->insert_id();
        return $last_insert_id;
    }

    
    /**
     * 
     * This function change the status of records and delete the records
     * @param String $collection
     * @param String $field
     * 
     * */
    public function activeInactiveCommon($collection = '', $field = '', $delete = TRUE) {
        $data = $_POST['checkbox_id'];
        $mode = $this->input->post('statusMode');
        for ($i = 0; $i <= count($data); $i++) {
            if ($data[$i] == 'on') {
                unset($data[$i]);
            }
        }
        if ($field == '_id') {
            $datanew = $data;
            $data = array();
            $k = 0;
            foreach ($datanew as $key => $value) {
                $data[$k] = MongoID($value);
                $k++;
            }
        }
        $newdata = array_values($data);
        $this->mongo_db->where_in($field, $newdata);
        if (strtolower($mode) == 'delete') {
            if ($delete === TRUE) {
               $this->mongo_db->delete_all($collection);
            } else if ($delete === FALSE) {
                $statusArr = array('status' => 'Deleted');
                $this->mongo_db->set($statusArr);
                $this->mongo_db->update_all($collection);
            }
        } else {
            $statusArr = array('status' => $mode);
            $this->mongo_db->set($statusArr);
            $this->mongo_db->update_all($collection);
        }
    }

    /**
     * 
     * Common select base on the where in conditions
     *
     * @param $condition = array('field','where_in Array');
     */
    public function get_selected_fields_where_in($collection, $conditionArr = array(), $fields = array(), $sortArr = array(), $limit = FALSE, $offset = FALSE, $likearr = array()) {
        $this->mongo_db->select($fields);

        if (!empty($conditionArr)) {
            $field = $conditionArr[0];
            $data = $conditionArr[1];
            $condition = $conditionArr[2];

            if (!empty($condition)) {
                $this->mongo_db->where($condition);
            }
            if ($field != '' && !empty($data)) {
                if ($field == '_id') {
                    $datanew = $data;
                    $data = array();
                    $k = 0;
                    foreach ($datanew as $key => $value) {
                        $data[$k] = MongoID($value);
                        $k++;
                    }
                }
                $newdata = array_values($data);
                $this->mongo_db->where_in($field, $newdata);
            }
        }

        if (!empty($likearr)) {
            if (count($likearr) > 0) {
                foreach ($likearr as $key => $val) {
                    $this->mongo_db->or_like($key, $val);
                }
            } else {
                $this->mongo_db->like($key, $val);
            }
        }
        if ($sortArr != '' && is_array($sortArr) && !empty($sortArr)) {
            $this->mongo_db->order_by($sortArr);
        }
        if ($limit !== FALSE && is_numeric($limit) && $offset !== FALSE && is_numeric($offset)) {
            $this->mongo_db->limit($limit);
            $this->mongo_db->offset($offset);
        } 
        $res = $this->mongo_db->get($collection);
        
        return $res;
    }
    
    /**
     * 
     * This function return the count of particular records
     * @param String $collection
     * @param Array $condition
     * @param Array $filterarr
     *
     * */
    public function get_all_counts($collection = '', $condition = array(), $filterarr = array(), $limit = FALSE, $offset = FALSE) {
        if (!empty($condition)) {
            $this->mongo_db->where($condition);
        }
        if (!empty($filterarr)) {
            if (count($filterarr) > 0) {
                foreach ($filterarr as $key => $val) {
                    $this->mongo_db->or_like($key, $val);
                }
            } else {
                $this->mongo_db->like($key, $val);
            }
        }
        if ($limit !== FALSE && is_numeric($limit) && $offset !== FALSE && is_numeric($offset)) {
            $this->mongo_db->limit($limit);
            $this->mongo_db->offset($offset);
        }
        return $this->mongo_db->count($collection);
    }

    /**
     * 
     * This function push the data in to a field
     * @param String $collection
     * @param Array $condition
     * @param Array/String $pushdata
     *
     * */
    public function simple_push($collection = '', $condition = array(), $pushdata = array()) {
        if (!empty($condition)) {
            $this->mongo_db->where($condition);
        }
        $this->mongo_db->push($pushdata);
        return $this->mongo_db->update_all($collection);
    }

    /**
     * 
     * This function removes the data in a field
     * @param String $collection
     * @param Array $condition
     * @param Array/String $pushdata
     *
     * */
    public function simple_pull($collection = '', $condition = array(), $pulldata, $value = array()) {
        if (!empty($condition)) {
            $this->mongo_db->where($condition);
        }
        if (is_array($pulldata)) {
            foreach ($pulldata as $field => $value) {
                $this->mongo_db->pull($field, $value);
            }
        } elseif (is_string($pulldata)) {
            $this->mongo_db->pull($pulldata, $value);
        }
        return $this->mongo_db->update_all($collection);
    }

    /**
     * 
     * This function add to set data in a field
     * @param String $collection
     * @param Array $condition
     * @param Array $setdata
     *
     * */
    public function set_to_field($collection = '', $condition = array(), $setdata = array()) {
        if (!empty($condition)) {
            $this->mongo_db->where($condition);
        }
        if (is_array($setdata)) {
            $this->mongo_db->set($setdata);
        }
        return $this->mongo_db->update_all($collection);
    }

    /**
     * 
     * This function calculate the distance between two lat lon
     * @param String $lat1
     * @param String $lon1
     * @param String $lat2
     * @param String $lon2
     * @param String $unit (M=>Miles,K=>Kilometers,N=>Nautical Miles)
     *
     * */
    public function geoDistance($lat1, $lon1, $lat2, $lon2, $unit = 'K') {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);

        if ($unit == "K") {
            return ($miles * 1.609344);
        } else if ($unit == "N") {
            return ($miles * 0.8684);
        } else {
            return $miles;
        }
    }

    /**
     * 
     * This function calculate the ETA (return in minutes)
     * @param String $distance km
     * @param String $speed in kmh
     *
     * */
    public function calculateETA($distance, $speed = 20) {
        $time = ($distance / $speed) * 60;
        if ($time > 0) {
            $eta = ceil($time);
            $eta = intval($eta);
        } else {
            $eta = 0;
        }
        return $eta;
    }
	
    /**
     * 
     * This function generate the ride id
     *
     * */
    public function get_consigment_id() {
		$digits = 6;
		$ride_id = str_pad(rand(0, pow(10, $digits)-1), $digits, '0', STR_PAD_LEFT);
        $condition = array('consignment_number' => $ride_id);
        $this->mongo_db->select(array('consignment_number'));
        $this->mongo_db->where($condition);
        $res = $this->mongo_db->get(CONSIGNMENT);
        
        if ($res->num_rows() > 0) {
            $check = 0;
            $ride_id = str_pad(rand(0, pow(10, $digits)-1), $digits, '0', STR_PAD_LEFT);
            while ($check == 0) {
                $condition = array('consignment_number' => $ride_id);
                $duplicate_id = $this->get_all_details(CONSIGNMENT, $condition);
                if ($duplicate_id->num_rows() > 0) {
                    $ride_id = str_pad(rand(0, pow(10, $digits)-1), $digits, '0', STR_PAD_LEFT);
                } else {
                    $check = 1;
                }
            }
        }
        return $ride_id;
    }
	public function get_trip_id() {
		$digits = 6;
		$ride_id = str_pad(rand(0, pow(10, $digits)-1), $digits, '0', STR_PAD_LEFT);
        $condition = array('trip_id' => $ride_id);
        $this->mongo_db->select(array('trip_id'));
        $this->mongo_db->where($condition);
        $res = $this->mongo_db->get(TRIP);
        
        if ($res->num_rows() > 0) {
            $check = 0;
            $ride_id = str_pad(rand(0, pow(10, $digits)-1), $digits, '0', STR_PAD_LEFT);
            while ($check == 0) {
                $condition = array('trip_id' => $ride_id);
                $duplicate_id = $this->get_all_details(TRIP, $condition);
                if ($duplicate_id->num_rows() > 0) {
                    $ride_id = str_pad(rand(0, pow(10, $digits)-1), $digits, '0', STR_PAD_LEFT);
                } else {
                    $check = 1;
                }
            }
        }
        return $ride_id;
    }
    /**
     * 
     * This function generate the random string
     *
     * */
    public function get_random_string($length = 6) {
        $six_digit_random_number = mt_rand(100000, 999999);
        return $six_digit_random_number;
    }
    
    public function get_random_number($length = 6) {
        $six_digit_random_number = mt_rand(100000, 999999);
        return $six_digit_random_number;
    }
	
	/**
	*
	* This functions updates the collection details using @param 
	* @param String $collection
	* @param Array $data
	* @param Array $condition
	*
	* */
    public function unset_details($collection = '', $fileds_data='', $condition = '') {
        if (!empty($condition)) {
            $this->mongo_db->where($condition);
        }
        if (is_array($fileds_data)) {
            $this->mongo_db->unset_field($fileds_data);
        }
        return $this->mongo_db->update_all($collection);
    }
    
    
    /* This function unset the field */
   public function unsetcommon($field="",$collection="",$id="") {
       if (!empty($condition)) {
            $condition=array('_id'=>MongoID($id));
            $this->mongo_db->where($condition);
        }
        if (is_array($field)) {
            $this->mongo_db->unset_field($field);
        }
        return $this->mongo_db->update_all($collection);
   }
	
	

}
?>