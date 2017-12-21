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
          </form>
           <div class="form-group row">
                 <div class="col-sm-9 offset-sm-3">
                   <div id="result">
                        
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




#result  {
    list-style: none !important;
    font-family: Courier;
    color: #0a0a0a;
    overflow: hidden;
  /*  border-bottom: 1px solid #a2a2a3;*/
    padding: 7px 0;
    line-height: 1.2;
}


#d-wrapper {
background-color: #fff;
margin-bottom: 2px;
}
#d-wrapper * {

margin:0;
padding:0;}
.center {text-align: center;}
#d-wrapper  div.sep {
    min-height: 200px;
    padding: 32px 0;

  }

#d-wrapper  .zig-zag-top:before{
    background: 
          linear-gradient(-45deg, #1ba1e2 16px, red 16px, blue 16px,  transparent 0), 
          linear-gradient(45deg, #1ba1e2 16px, transparent 0);
        background-position: left top;
        background-repeat: repeat-x;
        background-size: 22px 32px;
        content: " ";
        display: block;

        height: 32px;
    width: 100%;

    position: relative;
    bottom: 64px;
    left:0;
  }

  .downlink {color: #fff;
font-style: underline;
padding: 15px !important;
float: right;
margin: 20px;
font:arial;}

#d-wrapper  div > * {
   /* margin: 0 19px;*/
    text-align:center;
  }

#d-wrapper  .zig-zag-bottom{
    margin: 32px 0;
    margin-top: 0;
    background: #1ba1e2;
  }

#d-wrapper  .zig-zag-top{
    margin: 32px 0;
    margin-bottom: 0;
      background: #1ba1e2;
  }

#d-wrapper  .zig-zag-bottom,
#d-wrapper  .zig-zag-top{
        padding: 32px 0;
  }

#d-wrapper  h1{
      font-size:2em;
      text-align:center;
      color:#fff;
      font-family:"PT Sans Narrow", "Fjalla One", sans-serif;
      font-weight:900;
      text-shadow:1px 1px 0 #1b90e2, 2px 2px 0 #1b90e2, 3px 3px 0 #1b90e2, 4px 4px 0 #1b90e2, 5px 5px 0 #1b90e2;

  }

#d-wrapper  div.sep p,
#d-wrapper  div.sep h1 {
    text-shadow:1px 1px 0 #888, 2px 2px 0 #888, 3px 3px 0 #888, 4px 4px 0 #888, 5px 5px 0 #888;
    color: #fff;
  }

#d-wrapper  h1{
     font-size:4em;
  }

#d-wrapper  .zig-zag-bottom:after{
    background: 
          linear-gradient(-45deg, transparent 16px, #1ba1e2 0), 
          linear-gradient(45deg, transparent 16px, #1ba1e2  0);
        background-repeat: repeat-x;
    background-position: left bottom;
        background-size: 22px 32px;
        content: "";
        display: block;

    width: 100%;
    height: 32px;

      position: relative;
    top:64px;
    left:0px;
  }

#d-wrapper  p{
    text-align: center;
  }

#d-wrapper  p:not(:last-child) {
    margin-bottom: 20px;
  }


p {
  text-align: center;
  
  
}
.auth{
  text-decoration: overline;
  color: #999;
  font-size: 2em;
}


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