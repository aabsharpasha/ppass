<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
    
      <div class="container-fluid">
              <div class="row">
                <!-- Basic Form-->
                <div class="col-lg-12">
                  <div class="card">
                    
                    <div class="card-header d-flex align-items-center">
                      <h3 class="h4">Create New Vendor</h3>
                    </div>
                    <div class="card-body">
          <!-- Forms Section-->
          <section class="forms"> 
          <div class=""><?php echo validation_errors('<li>', '</li>') ?></div>
                <form class="form-horizontal" name="add_vendor" action="<?php echo  (isset($vendor->vendor_id) ? base_url('vendor/edit_vendor') : base_url('vendor/add_vendor')) ?>" method="post">
                    <div class="form-group">
                        <label class="control-label col-sm-2" for="vendor_name">Vendor Name</label>
                         <div class="col-sm-10">
                             <input type="text" class="form-control" id="vendor_name" name="vendor_name" placeholder="Vendor Name" value="<?php echo set_value('vendor_name', $vendor->vendor_name) ?>" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-2" for="vendor_email">Vendor Email</label>
                         <div class="col-sm-10">
                        <input type="email" class="form-control" id="vendor_email" placeholder="Vendor Email" name="vendor_email" value="<?php echo set_value('vendor_email', $vendor->vendor_email) ?>">
                        </div>
                    </div>
                     <div class="form-group">
                        <label class="control-label col-sm-2" for="vendor_address">Vendor Address</label>
                          <div class="col-sm-10">
                        <input type="text" class="form-control" id="vendor_address" placeholder="Vendor Address" name="vendor_address" value="<?php echo set_value('vendor_address', $vendor->vendor_address) ?>" required>
                        </div>
                    </div>
                     <div class="form-group">
                        <label class="control-label col-sm-2" for="inputPassword">Vendor Lat/Long</label>
                          <div class="col-sm-10">
                           <input type="text" class="form-control" id="vendor_lat" placeholder="Vendor Lat" name="vendor_lat" value="<?php echo set_value('vendor_lat', $vendor->vendor_lat) ?>" required>
                        
                          
                        <input type="text" class="form-control" id="vendor_long" placeholder="Vendor Long" name="vendor_long" value="<?php echo set_value('vendor_long', $vendor->vendor_long) ?>" required>
                        <input type="hidden" name="vendor_id" value="<?php echo $vendor->vendor_id ?>" />
                         </div>
                    </div>
 <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
                    <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                    </div>
                    
                </form>
          </section>
          </div>
          </div>
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
              "url": "<?php echo base_url('vendor/get_json_data'); ?>",
              "type": "POST",
              "data": function (response) {
                  //console.log(response)
                }
            }
          });
      } );
    </script>
