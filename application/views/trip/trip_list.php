  <?php  
    $this->load->view('templates/header.php');
    $this->load->view('templates/sidebar.php');
    ?>
	
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Trip List</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="<?php echo base_url().'vehicle/list'; ?>">Home</a></li>
              <li class="breadcrumb-item active">Trip List</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
		<div class="row">
          <div class="col-12">
		  <?php
				$attributes = array('class' => '', 'id' => 'ride_filter_form','method'=>'GET','autocomplete'=>'off','enctype' => 'multipart/form-data');
				echo form_open('trip/list', $attributes)
            ?>
			 <div class="row margin-20">
                  <div class="col-3">
                    <div class="input-group">
						<div class="input-group-prepend">
						  <span class="input-group-text">
							<i class="far fa-calendar-alt"></i>
						  </span>
						</div>
						<input type="text" class="form-control float-right" id="reservation" name="date_range" value="<?php if(isset($_GET['date_range']))echo $_GET['date_range']; ?>">
						
						
					  </div>
                  </div>
                  <div class="col-2">
                    <button type="submit" class="btn btn-info">Apply Filter</button>
							<?php if(isset($filter) && $filter!=""){ ?>
								<a href="<?php echo current_url(); ?>" class="btn btn-danger btn-circle btn-sm waves-effect waves-light" title="remove filter"><i class="fas fa-times-circle"></i></a>
							<?php } ?>
						   <button type="button" class="btn btn-success btn-circle btn-sm waves-effect waves-light" id="export_all_trip" title="Click to Export As Excel"><i class="fas fa-file-excel"></i></button>
                                    
                       
                  </div>
                  
           </div>
		    </form>  
			
            <div class="card">
              
              <!-- /.card-header -->
              <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                  <thead>
                    <tr>
                      <th>S.No</th>
                      <th>Trip Id</th>
                      <th>Vehicle Number</th>
                      <th>Vehicle Owner</th>
                      <th>Consignment No</th>
                      <th>Material Code</th>
                      <th>Weight</th>
                      <th>Shipper</th>
					  <th>Start Date</th>
					  <th>Booked Date</th>
                    </tr>
                  </thead>
                  <tbody>
					<?php 
						/* echo "<pre>";
						print_r($vehicle_list->result()); */
						$i=1;
					?>
					<?php if($trip_list->num_rows() >0) {
						   foreach($trip_list->result() as $data) {
					?>
					<tr>
                      <td><?php echo $i; ?></td>
                      <td><?php echo $data->trip_id; ?></td>
                      <td><?php echo $data->vehicle['vehicle_number']; ?></td>
                      <td><?php echo $data->vehicle['owner_name']; ?></td>
                      <td><?php echo $data->consigment['consigment_number']; ?></td>
                      <td><?php echo $data->consigment['material_code']; ?></td>
                      <td><?php echo $data->consigment['weight']; ?></td>
                      <td><?php echo $data->consigment['shipper_name']; ?></td>
                      <td><?php echo date('d-m-Y h:i a',MongoEPOCH($data->start_date)); ?></td>
                      <td><?php echo date('d-m-Y h:i a',MongoEPOCH($data->booked_date)); ?></td>
                    </tr>
					<?php $i++; } }?>
                    
                    
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
        </div>
        <!-- /.row -->
      

    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  <?php  
    $this->load->view('templates/footer.php');
    
  ?>
 <style>
	.margin-20 {
		margin: 20px;
	}
 </style>
 <script>
	$(function (){
		$("#export_all_trip").click(function(event){
			event.preventDefault();
			var query_strings = "<?php echo $_SERVER["QUERY_STRING"]; ?>";
			window.location.href = "<?php echo base_url(); ?>trip/list?" + query_strings + "&export=excel&export_type=all";
		});
		
	});
	
</script>