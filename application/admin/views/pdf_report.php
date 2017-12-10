<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//print_r($vendors); exit;
?>
<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Dashboard</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="all,follow">
    <?php $this->view('includes/head'); ?>
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/dataTables.bootstrap.min.css">
  </head>
  <body class="listing">
  <div class="page form-page">
  <header class="header">
        <nav class="navbar">
          <!-- Search Box-->
          <div class="search-box">
            <button class="dismiss"><i class="icon-close"></i></button>
            <form id="searchForm" action="#" role="search">
              <input type="search" placeholder="What are you looking for..." class="form-control">
            </form>
          </div>
          <div class="container-fluid">
            <div class="navbar-holder d-flex align-items-center justify-content-between">
              <!-- Navbar Header-->
              <div class="navbar-header">
                <!-- Navbar Brand --><a href="#" class="navbar-brand">
                  <div class="brand-text brand-big"><strong>PARKING PASS</strong></div>
                  <!-- <div class="brand-text brand-small"><strong>DB</strong></div></a> -->
                <!-- Toggle Button--><!-- <a id="toggle-btn" href="#" class="menu-btn active"><span></span><span></span> --><span></span></a>
              </div>
              <!-- Navbar Menu -->
              <ul class="nav-menu list-unstyled d-flex flex-md-row align-items-md-center">
                <!-- Search-->
                <!--<li class="nav-item d-flex align-items-center"><a id="search" href="#"><i class="icon-search"></i></a></li>
                <!-- Notifications-->
                
                <!-- Messages                        -->
                
                <!-- Logout    -->
               
              </ul>
            </div>
          </div>
        </nav>
      </header>
    
      <div class="container-fluid">
              <div class="row">
                <!-- Basic Form-->
                <div class="col-lg-6" style="margin:auto;">
                  <div class="card">
                    
                    <!-- <div class="card-header d-flex align-items-center">
                      <h3 class="h4">PARKING PASS</h3>
                    </div> -->
                  
                    <div class="card-body">
                      <div><?php echo $vendor_location->vendor_name ?> (<?php echo $vendor_location->vendor_id ?>)</div>
              <p><?php echo $vendor_location->vendor_address ?></p>
          <!-- Forms Section-->
            <section class="forms"> 
            
               <div class="form-group row">
                 
                     <div class="col-sm-9 offset-sm-3">
                        <div id="result">
                       
                            <ul>
                               <li class="list-download">
                                 
                                  <div><span>Duration: </span> <?php echo $checkin_details->duration_occupied ?></div>
                                  <div><span>Check In time: </span> <?php echo date("Y-m-d h:i A",strtotime($checkin_details->checkin_time)) ?></div>
                                  <div><span>Check Out time: </span> <?php echo date("Y-m-d h:i A",strtotime($checkin_details->checkout_time)) ?></div>
                                
                                  <div><span>Reciept No: </span><?php echo ' ppass_'.$checkin_details->checkin_id ?></div>
                               </li>
                           
                            </ul>
                              <h3>Vehicle No: <?php echo $checkin_details->vehicle_no ?><?php echo ($checkin_details->vehicle_model ? ' ('.$checkin_details->vehicle_model.')' : '') ?></h3>
  <h3>Amount Paid:  <?php echo $checkin_details->paid_amount ?></h3>
  

                        </div>
                       
                        </div>
              </div>
                    
              
          </section>
         
          </div>
          </div>
          </div>
          </div>
          </div>
        
    <!-- Javascript files-->
    <?php $this->view('includes/footer_js'); 
    $loader_url = base_url('assets/img/ajax-loader.gif');
    ?>
    <script src="<?php echo base_url(); ?>assets/js/jquery.dataTables.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/dataTables.bootstrap.min.js"></script>
    <script>
      $(document).ready(function() {

      $("#submit-list").click(function() {
        console.log('hit');
        var data = $('#recpt-form').serialize();
        //console.log(data);
        $('#result').html('<div id="loader"><img src="<?php echo $loader_url; ?>" />');
        $.ajax({
          url: "<?php echo base_url('vendor/get_reciept_list'); ?>",
          type: "POST",
          data:{'data': data},
          cache: false,
          success: function(res) {
           // $('#loader').hide();
            $('#result').html(res);
          }
        });

        return false;
      });
         
      } );
    </script>
    <style>.list-download{
margin: 5px;
list-style: none;}
.list-download div span {font-size: 17px !important;}
</style>



