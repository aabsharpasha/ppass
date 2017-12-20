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
                          <select  name="vendor_id"  id="vendors" required style="width:410px;" class="form-control">
                            <option value="">Select Vendor</option>
                              <?php foreach($vendors as $vendor) { ?>
                                
                                <option value="<?php echo $vendor->vendor_id ?>" <?php echo ($vendor->vendor_id == $user->vendor_id ? 'selected' : '') ?>><?php echo $vendor->vendor_name ?> (<?php echo $vendor->vendor_id ?>)</option>
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
                    <div id="result">
                       Fill above details and click Submit button to see Details
                    </div>
                  </div>
                    
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
    <style>.list-download{border-top: 1px solid;
padding: 5px;
margin: 5px;
list-style: none;

}
.selection {
  display: block !important;
}

#select2-vendors-container {
font-size: 15px !important;
}
.list-download div span {font-size: 17px !important;}

.select2-container--default .select2-selection--single {
 border: 1px solid #e6e6e6 !important;
 
  color:#777 !important;
}

.select2-container--default .select2-selection--single .select2-selection__rendered {
  color:none !important;
}

/* style for reciept start here */
.summary-block {
    background: url(<?php echo base_url('assets') ?>/img/order-summary-mid.png) left top no-repeat;
        background-size: auto auto;
    position: relative;
    padding: 4px 31px 0;
    background-size: 313px 100%;
    padding: 4px 16px 0;
}

ul li:first-child {
    border-top: 1px solid #a2a2a3;
}
#result h4 {
    font-size: 14px;
    /*font-family: 'interstatebold';*/
    text-align: center;
    text-transform: uppercase;
    margin: 12px 0 7px;

}

#result h5 {
    font-size: 12px;
    /*font-family: 'interstatebold';*/
    text-align: center;
    text-transform: uppercase;
    margin: 12px 0 7px;
}
.summary-block:after {
 background:url(<?php echo base_url('assets') ?>/img/order-summary-top.png) left top no-repeat;
 position:absolute;
 top:-12px;
 height:12px;
 left:1px;
 width:312px;
 content:"";
}
.summary-block .Order-logo {
    text-align: center;
}
 .order-summary {
    
  /*  float: right;*/
    color: #0a0a0a;
    background: url(<?php echo base_url('assets') ?>/img/order-summary-bottom.png) left bottom no-repeat; padding-bottom: 62px;
    margin-top: 12px;
    margin-right: 94px;
}

#result .order-summary {
    color: #0a0a0a;
}

.center {text-align:center;}



#result ul li {
    list-style: none !important;
    font-family: Courier;
    color: #0a0a0a;
    overflow: hidden;
  /*  border-bottom: 1px solid #a2a2a3;*/
    padding: 7px 0;
    line-height: 1.2;
}

#result {background: grey;
padding: 20px 0px 20px 5px;}

</style>

 <!--   <script src="https://code.jquery.com/jquery-2.1.1.min.js" type="text/javascript"></script> -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/js/select2.min.js"></script>
    <script type="text/javascript">
      $(document).ready(function() {
        var country = [<?php echo $vendor_list_comma ?>];
        $("#vendors").select2({
          data: country
        });
      });
    </script>
</html>