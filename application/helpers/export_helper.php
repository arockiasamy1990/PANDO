<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	
if (!function_exists('export_trip_list')){
	function export_trip_list($rideDetails = array()){
		$limit = 500;
		$ci =& get_instance();
		$ci->load->library(array('excel'));
		#echo "<pre>";print_r($rideDetails->result());die;
		$rideArray = $rideDetails->result_array();
		$no_of_rows = count($rideDetails->result_array());
		$no_of_sheets = floor($no_of_rows/$limit);
		if(($no_of_rows%$limit) > 0){
			$no_of_sheets++;
		}
		$headers_array = array('Trip ID','Vehicle Number','Vehicle Maker','Vehicle Model','Vehicle Owner Name','vehicle Owner Contact','Consignment Number','Material Code','Weight','Shipper Name','Shipper Contact','Start Date','Booked Date');
	
		$next_limit = 0;
		for($i=0; $i<$no_of_sheets; $i++){
			$ci->excel->setActiveSheetIndex($i);
			$current_limit = $next_limit;
			/* Setting Header Name */
			$headerLetter = 'A';
			foreach($headers_array as $key => $val){
			$ci->excel->getActiveSheet()->setCellValue($headerLetter++."1", $val);
			}
			$ci->excel->getActiveSheet()->getStyle('A1:'.$headerLetter.'1')->getFont()->setBold(true);
			$ci->excel->getActiveSheet()->getStyle('A1:'.$headerLetter.'1')->getFont()->setSize(12);
			
			/* Setting Header Name --- Ends here */
			
			$m = $i+1;
			$next_limit = $m*$limit;
			$row = 2;
			foreach($rideArray as $key => $val){
				if($key >= $current_limit && $key < $next_limit){
					$contentLetter = 'A';
					$trip_id = (string)$rideArray[$key]['trip_id'];
					$ci->excel->getActiveSheet()->setCellValue($contentLetter.$row, $trip_id);
					$contentLetter++;
					
					$vehicle_number = (string)$rideArray[$key]['vehicle']['vehicle_number'];
					$ci->excel->getActiveSheet()->setCellValue($contentLetter.$row, $vehicle_number);
					$ci->excel->getActiveSheet()->getColumnDimension($contentLetter)->setWidth(20);
					$contentLetter++;
					
					$vehicle_maker = (string)$rideArray[$key]['vehicle']['vehicle_maker'];
					$ci->excel->getActiveSheet()->setCellValue($contentLetter.$row, $vehicle_maker);
					$ci->excel->getActiveSheet()->getColumnDimension($contentLetter)->setWidth(15);
					$contentLetter++;
					
					$vehicle_model = (string)$rideArray[$key]['vehicle']['vehicle_model'];
					$ci->excel->getActiveSheet()->setCellValue($contentLetter.$row, $vehicle_model);
					$ci->excel->getActiveSheet()->getColumnDimension($contentLetter)->setWidth(15);
					$contentLetter++;
					
					$owner_name = (string)$rideArray[$key]['vehicle']['owner_name'];
					$ci->excel->getActiveSheet()->setCellValue($contentLetter.$row, $owner_name);
					$ci->excel->getActiveSheet()->getColumnDimension($contentLetter)->setWidth(25);
					$contentLetter++;
					
					$owner_contact = (string)$rideArray[$key]['vehicle']['owner_contact'];
					$ci->excel->getActiveSheet()->setCellValue($contentLetter.$row, $owner_contact);
					$ci->excel->getActiveSheet()->getColumnDimension($contentLetter)->setWidth(25);
					$contentLetter++;
					
					$consigment_number = (string)$rideArray[$key]['consigment']['consigment_number'];
					$ci->excel->getActiveSheet()->setCellValue($contentLetter.$row, $consigment_number);
					$ci->excel->getActiveSheet()->getColumnDimension($contentLetter)->setWidth(25);
					$contentLetter++;
					
					$material_code = (string)$rideArray[$key]['consigment']['material_code'];
					$ci->excel->getActiveSheet()->setCellValue($contentLetter.$row, $material_code);
					$ci->excel->getActiveSheet()->getColumnDimension($contentLetter)->setWidth(15);
					$contentLetter++;
					
					$weight = (string)$rideArray[$key]['consigment']['weight'];
					$ci->excel->getActiveSheet()->setCellValue($contentLetter.$row,$weight);
					$ci->excel->getActiveSheet()->getColumnDimension($contentLetter)->setWidth(15);
					$contentLetter++;
					
					$shipper_name = (string)$rideArray[$key]['consigment']['shipper_name'];
					$ci->excel->getActiveSheet()->setCellValue($contentLetter.$row,$shipper_name);
					$ci->excel->getActiveSheet()->getColumnDimension($contentLetter)->setWidth(20);
					$contentLetter++;
					
					$shipper_contact = (string)$rideArray[$key]['consigment']['shipper_contact'];
					$ci->excel->getActiveSheet()->setCellValue($contentLetter.$row,$shipper_contact);
					$ci->excel->getActiveSheet()->getColumnDimension($contentLetter)->setWidth(20);
					$contentLetter++;
					
					
					
					if(isset($rideArray[$key]['start_date']) && $rideArray[$key]['start_date'] != ''){
						$start_date = date('Y-m-d H:i:s',MongoEPOCH($rideArray[$key]['start_date']));
					}else{
						$start_date = 'NA';
					}
					$ci->excel->getActiveSheet()->setCellValue($contentLetter.$row, $start_date);
					$ci->excel->getActiveSheet()->getColumnDimension($contentLetter)->setWidth(20);
					$contentLetter++;
					
					
					if(isset($rideArray[$key]['booked_date']) && $rideArray[$key]['booked_date'] != ''){
						$booked_date = date('Y-m-d H:i:s',MongoEPOCH($rideArray[$key]['booked_date']));
					}else{
						$booked_date = 'NA';
					}
					$ci->excel->getActiveSheet()->setCellValue($contentLetter.$row, $booked_date);
					$ci->excel->getActiveSheet()->getColumnDimension($contentLetter)->setWidth(20);
					$contentLetter++;
					
					
					
					
					
					$row = $row +1;;
				}
				
			} 
			
		/* Creating Multiple Sheets*/
		$sheet_index = $i+1;
		$ci->excel->getActiveSheet()->setTitle('sheet'.$sheet_index);
		$ci->excel->createSheet();
		
		}

		$filename= 'Trip Report '.date("Y-m-d").'.xls'; //save our workbook as this file name
		header('Content-Type: application/vnd.ms-excel'); //mime type
		header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
		header('Cache-Control: max-age=0'); //no cache
					 
		//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
		//if you want to save it as .XLSX Excel 2007 format
		$objWriter = PHPExcel_IOFactory::createWriter($ci->excel, 'Excel5');  
		//force user to download the Excel file without writing it to server's HD
		$objWriter->save('php://output');
		exit;
	}
}
