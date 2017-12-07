<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//print_r($vendors); exit;
?>
    
      <div class="container-fluid">
              <div class="row">
                <!-- Basic Form-->
                <div class="col-lg-6" style="margin:auto;">
                  <div class="card">
                    
                    <div class="card-header d-flex align-items-center">
                      <h3 class="h4">Download Reciept</h3>
                    </div>
                    <div class="card-body">
          <!-- Forms Section-->
          <section class="forms"> 
          <div class=""><?php echo validation_errors('<li>', '</li>') ?></div>
                <form id = 'recpt-form' class="form-horizontal" name="add_user" action="<?php echo base_url('vendor/get_reciept_list') ?>" method="post">
                    <div class="form-group row">
                        <label class="col-sm-3 form-control-label" for="vendor_name">Choose Vendor</label>
                         <div class="col-sm-9">
                          <select name="vendor_id" class="form-control" required>
                            <option value="">Select Vendor</option>
                              <?php foreach($vendors as $vendor) { ?>
                                
                                <option value="<?php echo $vendor->vendor_id ?>" <?php echo ($vendor->vendor_id == $user->vendor_id ? 'selected' : '') ?>><?php echo $vendor->vendor_name ?></option>
                              <?php } ?>
                          </select>
                             <!-- <input type="text" class="form-control form-control-success" id="vendor_name" name="vendor_name" placeholder="Vendor Name" value="<?php echo set_value('vendor_name', $vendor->vendor_name) ?>" required> -->
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 form-control-label"  for="vendor_email">Vehicle Number</label>
                         <div class="col-sm-9">
                        <input type="text" class="form-control" id="user_email" placeholder="Vehicle Number" name="vehicle_number" value="<?php echo set_value('vehicle_number', $user->vehicle_number) ?>">
                        </div>
                    </div>
                     <div class="form-group row">
                        <label class="col-sm-3 form-control-label"  for="vendor_address">User Pin</label>
                           <div class="col-sm-9">
                        <input type="text" class="form-control" id="vehicle_pin" placeholder="Vehicle Pin" name="vehicle_pin" value="<?php echo set_value('vehicle_pin', $user->vehicle_pin) ?>" required>
                        </div>
                    </div>
                     
          <div class="form-group row">
                 <div class="col-sm-4 offset-sm-3">
                    <button type="button" class="btn btn-primary" id="submit-list">Submit</button>
                     <!--  <a href="<?php echo base_url('vendor/users') ?>" id="cancel" name="cancel" class="btn btn-default">Cancel</a> -->
                    </div>
          </div>

           <div class="form-group row">
                 <div class="col-sm-9 offset-sm-3">
                    <div id="result"></div>
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

      $("#submit-list").click(function() {
        console.log('hit');
        var data = $('#recpt-form').serialize();
        console.log(data);
        $.ajax({
          url: "<?php echo base_url('vendor/get_reciept_list'); ?>",
          type: "POST",
          data:{'data': data},
          cache: false,
          success: function(res) {
            $('#result').html(res);
          }
        });

        return false;
      });
         
      } );
    </script>
    <style>.list-download{border-top: 1px solid;
padding: 5px;
margin: 5px;
list-style: none;}
.list-download div span {font-size: 17px !important;}
</style>
