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
                      <h3 class="h4">Vendor List</h3>
                    </div>
                    <div class="table-responsive">
                    <a href="<?php echo base_url('vendor/add_vendor') ?>">Add Vendor</a>
                     <div><?php echo $this->session->flashdata('vendor_add_msg'); ?></div>
                      <table id="list" class="table table-striped table-bordered" cellspacing="0" width="100%">
                        <thead>
                          <tr>
                            <th>#</th>
                            <th>Vendor Id</th>
                            <th>Vendor Name</th>
                            <th>Vendor Address</th>
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
              "url": "<?php echo base_url('vendor/get_json_data'); ?>",
              "type": "POST",
              "data": function (response) {
                  //console.log(response)
                }
            },
            
           
            
            
          });
      } );
    </script>
