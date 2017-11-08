<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="page-content d-flex align-items-stretch">
<nav class="side-navbar">
          <!-- Sidebar Header-->
          <div class="sidebar-header d-flex align-items-center">
            <div class="avatar"><img src="<?php echo base_url(); ?>assets/img/user.png" alt="..." class="img-fluid rounded-circle"></div>
            <div class="title">
              <h1 class="h4">admin</h1>
              <p>Administrator</p>
            </div>
          </div>
          <!-- Sidebar Navidation Menus-->
          <?php
          $uri = $this->uri->segment(2);
          if($uri == 'add_vendor' || $uri == 'edit_vendor' || $uri == 'listing') {
            $class_vendor = 'active';
          } else {
            $class= '';
          }
          ?>
          <ul class="list-unstyled">
            <li class="<?php echo ($uri == 'add_vendor' || $uri == 'edit_vendor' || $uri == 'listing' ? 'active' : '') ?>"><a  href="<?php echo base_url('vendor') ?>"><i class="icon-interface-windows"></i>Manage Vendor</a></li>
                <li class="<?php echo ($uri == 'add_user' || $uri == 'edit_user' || $uri == 'users' ? 'active' : '') ?>"><a href="<?php echo base_url('vendor/users') ?>"><i class="icon-interface-windows"></i>Manage User</a></li>
                <li class="<?php echo ($uri == 'add_pricing' || $uri == 'edit_pricing' || $uri == 'pricing' ? 'active' : '') ?>"><a href="<?php echo base_url('vendor/pricing') ?>"><i class="icon-interface-windows"></i>Manage Pricing</a></li>
            <!-- <li><a href="#dashvariants" aria-expanded="false" data-toggle="collapse"> <i class="icon-interface-windows"></i>Vendor </a>
              <ul id="dashvariants" class="collapse list-unstyled">
                <li><a href="<?php echo base_url('vendor') ?>">Manage Vendor</a></li>
                <li><a href="<?php echo base_url('vendor/users') ?>">Manage User</a></li>
                <li><a href="<?php echo base_url('vendor/pricing') ?>">Manage Pricing</a></li>
               <!--  <li><a href="#">Page</a></li> 
              </ul>
            </li> -->
            <!-- <li> <a href="tables.html"> <i class="icon-grid"></i>Tables </a></li>
            <li> <a href="charts.html"> <i class="fa fa-bar-chart"></i>Charts </a></li>
            <li> <a href="forms.html"> <i class="icon-padnote"></i>Forms </a></li>
            <li> <a href="login.html"> <i class="icon-interface-windows"></i>Login Page</a></li> -->
          </ul>
          <!--<span class="heading">Extras</span>
          <ul class="list-unstyled">
            <li> <a href="#"> <i class="icon-flask"></i>Demo </a></li>
            <li> <a href="#"> <i class="icon-screen"></i>Demo </a></li>
            <li> <a href="#"> <i class="icon-mail"></i>Demo </a></li>
            <li> <a href="#"> <i class="icon-picture"></i>Demo </a></li>
          </ul>-->
        </nav>
        <div class="content-inner">