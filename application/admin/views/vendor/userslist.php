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
                      <h3 class="h4">Users List</h3>
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
                       <a href="<?php echo base_url('vendor/add_user') ?>" class="btn btn-default">Add User</a>
                      <table id="list" class="table table-striped table-bordered" cellspacing="0" width="100%">
                        <thead>
                          <tr>
                            <th>#</th>
                            <th>Vendor</th>
                            <th>User Name</th>
                            <th>User Email</th>
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
              "url": "<?php echo base_url('vendor/get_json_data_user'); ?>",
              "type": "POST",
              "data": function (response) {
                  //console.log(response)
                }
            },
            
           
            
            
          });
      } );
    </script>
