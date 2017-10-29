<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
        <!-- Side Navbar -->
        <?php $this->view('includes/sidebar_admin'); ?>
        <div class="content-inner">
          <!-- Page Header-->
          <header class="page-header">
            <div class="container-fluid">
              <h2 class="no-margin-bottom">Vendor Listing</h2>
            </div>
          </header>

          <!-- Forms Section-->
          <section class="forms"> 
            <div class="container-fluid">
              <div class="row">
                <!-- Basic Form-->
                <div class="col-lg-6">
                  <div class="card">
                    
                    <div class="card-header d-flex align-items-center">
                      <h3 class="h4">Upload File</h3>
                    </div>
                    <div class="card-body">
                      <p>Upload an excel file.</p>
                      <form name="upload_excel" id="excel_upload" action="<?php echo base_url(); ?>admin/upload_file" method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <input type="file" name="excelfile" id="excelfile" class="form-control">
                        </div>
                        <div class="form-group">       
                            <input type="submit" name="upload" value="Upload" id="upload_excl" class="btn btn-primary">
                        </div>
                      </form>
                      <?php 
                      $error = $this->session->flashdata('msg'); 
                      if($error['status'] == 0) {
                      ?>
                        <p style="color:red"><?php echo $error['message']; ?></p>
                      <?php } else { ?>
                        <p style="color:green"><?php echo $error['message']; ?></p>
                      <?php } ?>
                    </div>
                  </div>
                </div>
                <!-- Horizontal Form-->                
              </div>
            </div>
          </section>
          <!-- Page Footer-->
         
       
    <!-- Javascript files-->
    <?php $this->view('includes/footer_js'); ?>

</body>
</html>