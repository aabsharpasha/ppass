<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//print_r($vendors); exit;
?>
    
      <div class="container-fluid">
              <div class="row">
                <!-- Basic Form-->
                <div class="col-lg-12">
                  <div class="card">
                    
                    <div class="card-header d-flex align-items-center">
                      <h3 class="h4">Create New User</h3>
                    </div>
                    <div class="card-body">
          <!-- Forms Section-->
          <section class="forms"> 
          <div class=""><?php echo validation_errors('<li>', '</li>') ?></div>
                <form class="form-horizontal" name="add_user" action="<?php echo  (isset($user->user_id) ? base_url('vendor/edit_user') : base_url('vendor/add_user')) ?>" method="post">
                    <div class="form-group row">
                        <label class="col-sm-3 form-control-label" for="vendor_name">Choose Vendor</label>
                         <div class="col-sm-9">
                          <select name="vendor_id" class="form-control" required>
                            <option value="">Select Vendor</option>
                              <?php foreach($vendors as $vendor) { ?>
                                
                                <option value="<?php echo $vendor->vendor_id ?>"><?php echo $vendor->vendor_name ?></option>
                              <?php } ?>
                          </select>
                             <!-- <input type="text" class="form-control form-control-success" id="vendor_name" name="vendor_name" placeholder="Vendor Name" value="<?php echo set_value('vendor_name', $vendor->vendor_name) ?>" required> -->
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 form-control-label"  for="vendor_email">User Email</label>
                         <div class="col-sm-9">
                        <input type="email" class="form-control" id="user_email" placeholder="Email" name="email" value="<?php echo set_value('email', $user->email) ?>">
                        </div>
                    </div>
                     <div class="form-group row">
                        <label class="col-sm-3 form-control-label"  for="vendor_address">User Name</label>
                           <div class="col-sm-9">
                        <input type="text" class="form-control" id="vendor_address" placeholder="Username" name="user_name" value="<?php echo set_value('user_name', $user->user_name) ?>" required>
                        </div>
                    </div>
                     <div class="form-group row">
                        <label class="col-sm-3 form-control-label" for="user_pass">Password</label>
                           <div class="col-sm-9">
                           <input type="text" class="form-control" id="vendor_lat" placeholder="Passowrd" name="password" value="" required>
                        
                          
                        <input type="hidden" name="user_id" value="<?php echo $user->user_id ?>" />
                         </div>
                    </div>
 <div class="form-group row">
    <div class="col-sm-4 offset-sm-3">
                    <button type="submit" class="btn btn-primary">Submit</button>
                      <a href="<?php echo base_url('vendor/users') ?>" id="cancel" name="cancel" class="btn btn-default">Cancel</a>
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
