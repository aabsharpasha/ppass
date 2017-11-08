<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
    
        
          <!-- Page Header-->

          <!-- Forms Section-->
          <section class="forms"> 
            <div class="container-fluid">
              <div class="row">
                <!-- Basic Form-->
                <div class="col-lg-12">
                   <div class="card">
                    <div class="card-header d-flex align-items-center">
                      <h3 class="h4">Pricing</h3>
                    </div>
                    <div class="table-responsive">
                     <?php 
                      $message = $this->session->flashdata('vendor_add_msg');
                      if($message) { 
                      ?>
                      <div class="alert alert-success">
                          <strong>Success!</strong><?php echo $message ?>
                      </div>
                      <?php } ?>
                       <a href="<?php echo base_url('vendor/add_pricing') ?>" class="btn btn-default">Add New Pricing</a>
                      <table id="list" class="table table-striped table-bordered" cellspacing="0" width="100%">
                        <thead>
                          <tr>
                            <th>#</th>
                            <th>Vendor</th>
                            <th>Big Inventory</th>
                            <th>Big Pricing</th>
                            <th>Small Inventory</th>
                            <th>Small Pricing</th>
                            <th>Action</th>
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
              "url": "<?php echo base_url('vendor/get_json_data_pricing'); ?>",
              "type": "POST",
              "data": function (response) {
                  //console.log(response)
                }
            },
            
           
            
            
          });
      } );
    </script>
