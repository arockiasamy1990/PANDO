<body class="hold-transition sidebar-mini">
<?php  $currentUrl = $this->uri->segment(1, 0); ?>
<div class="wrapper">
 

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="<?php echo base_url().'vehicle/list'; ?>" class="brand-link">
      <img src="<?php echo base_url(); ?>dist/img/AdminLTELogo.png"
           alt="AdminLTE Logo"
           class="brand-image img-circle elevation-3"
           style="opacity: .8">
      <span class="brand-text font-weight-light">Vehicle Tracking</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
     

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
          
          <li class="nav-item">
            <a href="<?php echo base_url().'vehicle/list'; ?>" class="nav-link <?php if($currentUrl=='vehicle') echo "active" ?>">
             
			  <i class="nav-icon fas fa-truck"></i>
              <p>
                Vehicles Report
                
              </p>
            </a>
          </li>
		  <li class="nav-item">
            <a href="<?php echo base_url().'shipper/list'; ?>" class="nav-link <?php if($currentUrl=='shipper') echo "active" ?>">
              <i class="nav-icon fas fa-address-card"></i>
              <p>
                Shipper Report
                
              </p>
            </a>
          </li>
		  <li class="nav-item">
            <a href="<?php echo base_url().'trip/list'; ?>" class="nav-link <?php if($currentUrl=='trip') echo "active" ?>">
              <i class="nav-icon fas fa-luggage-cart"></i>
              <p>
                Trip Report
                
              </p>
            </a>
          </li>
         
         
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>