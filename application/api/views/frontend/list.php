<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
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
      <!-- Main Navbar-->
      <?php $this->view('includes/header_front'); ?>
      <div class="page-content d-flex align-items-stretch"> 
        <!-- Side Navbar -->
        
        <div class="content-inner full_width_container">
          <!-- Page Header-->

          <!-- Forms Section-->
          <section class="forms"> 
            <div class="container-fluid">
              <div class="row">
                <!-- Basic Form-->
                <div class="col-lg-12">
                   <div class="card">
                    <div class="card-header d-flex align-items-center">
                      <h3 class="h4">Basic Table</h3>
                    </div>
                    <div class="table-responsive">
                      <table id="list" class="table table-striped table-bordered" cellspacing="0" width="100%">
                        <thead>
                          <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>URL</th>
                            <th>Others</th>
                          </tr>
                        </thead>
                        <tbody>
                          
                        </tbody>
                      </table>
                        <?php //echo $pagination; ?>
                    </div>
                  </div>
                </div>
                <!-- Horizontal Form-->
                
                <!-- Inline Form-->
                
                <!-- Modal Form-->
                
                <!-- Form Elements -->
                
              </div>
            </div>
          </section>
          <!-- Page Footer-->
          <?php $this->view('includes/footer'); ?>
        </div>
      </div>
    </div>
    <!-- Javascript files-->
    <?php $this->view('includes/footer_js'); ?>
    <script src="<?php echo base_url(); ?>assets/js/jquery.dataTables.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/dataTables.bootstrap.min.js"></script>
    <script>
      $(document).ready(function() {
          $('#list').DataTable({
            "ordering": false,
            "info": false,
            "processing": true,
            "serverSide": true,
            //"stateSave": true,
            //"pagingType": "full_numbers",
            "ajax": {
              "url": "<?php echo base_url('listing/get_json_data'); ?>",
              "type": "POST",
              "data": function (response) {
                  //console.log(response)
                }
            }
          });
      } );
    </script>
</body>
</html>