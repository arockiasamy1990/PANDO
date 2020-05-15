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
            <h1>Vehicle List</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="<?php echo base_url().'vehicle/list'; ?>">Home</a></li>
              <li class="breadcrumb-item active">Vehicle List</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
		<div class="row">
          <div class="col-12">
            <div class="card">
              
              <!-- /.card-header -->
              <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                  <thead>
                    <tr>
                      <th>S.No</th>
                      <th>Vehicle Number</th>
                      <th>Maker</th>
                      <th>Model</th>
                      <th>Max capactiy</th>
                      <th>Owner Name</th>
                      <th>Owner ContactNumber</th>
					   <th>Created At</th>
                    </tr>
                  </thead>
                  <tbody>
					<?php 
						/* echo "<pre>";
						print_r($vehicle_list->result()); */
						$i=1;
					?>
					<?php if($vehicle_list->num_rows() >0) {
						   foreach($vehicle_list->result() as $data) {
					?>
					<tr>
                      <td><?php echo $i; ?></td>
                      <td><?php echo $data->vehicle_number; ?></td>
                      <td><?php echo $data->maker; ?></td>
                      <td><?php echo $data->model; ?></td>
                      <td><?php echo $data->maximum_capacity; ?></td>
                      <td><?php echo $data->owner_name; ?></td>
                      <td><?php echo $data->owner_contact; ?></td>
                      <td><?php echo date('d-m-Y h:i a',MongoEPOCH($data->created_at)); ?></td>
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