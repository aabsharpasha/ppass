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
                      <h3 class="h4">Pricing</h3>
                    </div>
                    <div class="card-body">
          <!-- Forms Section-->
          <section class="forms"> 
          <div class=""><?php echo validation_errors('<li>', '</li>') ?></div>
                <form class="form-horizontal" name="add_pricing" action="<?php echo  (isset($pricing->pricing_id) ? base_url('vendor/edit_pricing') : base_url('vendor/add_pricing')) ?>" method="post">
                    <div class="form-group row">
                        <label class="col-sm-3 form-control-label" for="vendor_name">Choose Vendor</label>
                         <div class="col-sm-9">
                          <select name="vendor_id" class="form-control" required>
                            <option value="">Select Vendor</option>
                              <?php foreach($vendors as $vendor) { ?>
                                
                                <option value="<?php echo $vendor->vendor_id ?>" <?php echo ($vendor->vendor_id == $pricing->vendor_id ? 'selected' : '') ?>><?php echo $vendor->vendor_name ?></option>
                              <?php } ?>
                          </select>
                             <!-- <input type="text" class="form-control form-control-success" id="vendor_name" name="vendor_name" placeholder="Vendor Name" value="<?php echo set_value('vendor_name', $vendor->vendor_name) ?>" required> -->
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 form-control-label"  for="vendor_email">Big Inventory</label>
                         <div class="col-sm-9">
                        <input type="number" class="form-control" id="user_email" placeholder="Big Inventory" name="big_inventory" value="<?php echo set_value('big_inventory', $pricing->big_inventory) ?>" required>
                        </div>
                          <label class="col-sm-3 form-control-label"  for="vendor_email">Initial Hour</label>
                         <div class="col-sm-9">
                        <input type="number" class="form-control" id="user_email" placeholder="Initial Hour" name="big_first_hours" value="<?php echo set_value('big_first_hours', $pricing->big_first_hours) ?>" required>
                        </div>
                          <label class="col-sm-3 form-control-label"  for="vendor_email">Initial Hour Rate</label>
                         <div class="col-sm-9">
                        <input type="number" class="form-control" id="big_first_hr_rate" placeholder="Initial Hour Rate" name="big_first_hr_rate" value="<?php echo set_value('big_first_hr_rate', $pricing->big_first_hr_rate) ?>" required>
                        </div>
                         <label class="col-sm-3 form-control-label"  for="vendor_email">Hourly Rate Post Initial Hour</label>
                         <div class="col-sm-9">
                        <input type="number" class="form-control" id="big_hourly_rate" placeholder="Hourly Rate Post Inital Hour" name="big_hourly_rate" value="<?php echo set_value('big_hourly_rate', $pricing->big_hourly_rate) ?>" required>
                        </div>
                    </div>

                    <br />
                    <div class="form-group row">
                        <label class="col-sm-3 form-control-label"  for="vendor_email">Small Inventory</label>
                         <div class="col-sm-9">
                        <input type="number" class="form-control" id="small_inventory" placeholder="Small Inventory" name="small_inventory" value="<?php echo set_value('small_inventory', $pricing->small_inventory) ?>" required>
                        </div>
                          <label class="col-sm-3 form-control-label"  for="vendor_email">Initial Hour</label>
                         <div class="col-sm-9">
                        <input type="number" class="form-control" id="user_email" placeholder="Initial Hour" name="small_first_hours" value="<?php echo set_value('big_first_hours', $pricing->small_first_hours) ?>" required>
                        </div>
                          <label class="col-sm-3 form-control-label"  for="vendor_email">Initial Hour Rate</label>
                         <div class="col-sm-9">
                        <input type="number" class="form-control" id="small_first_hr_rate" placeholder="Initial Hour Rate" name="small_first_hr_rate" value="<?php echo set_value('small_first_hr_rate', $pricing->small_first_hr_rate) ?>" required>
                        </div>
                         <label class="col-sm-3 form-control-label"  for="vendor_email">Hourly Rate Post Initial Hour</label>
                         <div class="col-sm-9">
                        <input type="number" class="form-control" id="small_hourly_rate" placeholder="Hourly Rate Post Inital Hour" name="small_hourly_rate" value="<?php echo set_value('small_hourly_rate', $pricing->small_hourly_rate) ?>" required>
                        </div>
                    </div>
                  <input type="hidden" name="pricing_id" value="<?php echo $pricing->pricing_id ?>" />
 <div class="form-group row">
    <div class="col-sm-4 offset-sm-3">
                    <button type="submit" class="btn btn-primary">Submit</button>
                      <a href="<?php echo base_url('vendor/pricing') ?>" id="cancel" name="cancel" class="btn btn-default">Cancel</a>
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
