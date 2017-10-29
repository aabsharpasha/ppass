<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Home</title>
     <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="all,follow">
    <?php $this->view('includes/head'); ?>
</head>
<body>

<div class="page login-page">
      <div class="container d-flex align-items-center">
        <div class="form-holder has-shadow">
          <div class="row">
            <!-- Logo & Information Panel-->
            <div class="col-lg-6">
              <div class="info d-flex align-items-center">
                <div class="content">
                  <div class="logo">
                    <h1>Login</h1>
                  </div>
                  <p>PASS..........</p>
                </div>
              </div>
            </div>
            <!-- Form Panel    -->
            <div class="col-lg-6 bg-white">
              <div class="form d-flex align-items-center">
                <div class="content">
                    <form id="login-form" method="post" action="<?php echo base_url(); ?>login">
                    <div class="form-group">
                      <input id="login-username" type="text" name="user_name" required="" value="" class="input-material">
                      <label for="login-username" class="label-material">User Name</label>
                    </div>
                    <div class="form-group">
                      <input id="login-password" type="password" name="password" required="" value="" class="input-material">
                      <label for="login-password" class="label-material">Password</label>
                    </div>
                      <input type="submit" name="login" id="login" value="Login" class="btn btn-primary">
                  </form>
                    <!-- This should be submit button but I replaced it with <a> for demo purposes-->
                    <!--<a href="#" class="forgot-pass">Forgot Password?</a><br><small>Do not have an account? </small><a href="register.html" class="signup">Signup</a>-->
                 <?php 
                    $error = $this->session->flashdata('login_msg'); 
                    if(!empty($error)) { ?>
                    <span style="color:red; clear: both;"><?php echo $error; ?></span>
                  <?php } ?>
                </div> 
              </div>
            </div>
          </div>
        </div>
      </div>
</div>
<!-- Javascript files-->
  <?php $this->view('includes/footer_js'); ?>
</body>
</html>