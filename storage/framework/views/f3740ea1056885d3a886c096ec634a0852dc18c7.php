<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>点畅在线报表系统</title>
  <!-- plugins:css -->
  <link rel="stylesheet" href="/assets/vendors/iconfonts/mdi/css/materialdesignicons.min.css">
  <link rel="stylesheet" href="/assets/vendors/css/vendor.bundle.base.css">
  <!-- endinject -->
  <!-- inject:css -->
  <link rel="stylesheet" href="/assets/css/style.css">
  <!-- endinject -->
  <link rel="shortcut icon" href="/assets/images/favicon.png" />
  <style>
    /*定义滚动条高宽及背景 高宽分别对应横竖滚动条的尺寸*/
    ::-webkit-scrollbar
    {
      width: 0px;
      height: 0px;
      background-color: #F5F5F5;
    }
  </style>
</head>
<body>
<div class="container-scroller">
  <!-- partial:partials/_navbar.html -->
  <nav class="navbar default-layout-navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
    <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
      <a class="navbar-brand brand-logo" href="/">点畅在线报表系统</a>
      <!-- <a class="navbar-brand brand-logo-mini" href="/"><img src="/assets/images/logo-mini.svg" alt="logo"/></a> -->
    </div>
    <div class="navbar-menu-wrapper d-flex align-items-stretch">
      <!-- <div class="search-field d-none d-md-block">
        <form class="d-flex align-items-center h-100" action="#">
          <div class="input-group">
            <div class="input-group-prepend bg-transparent">
              <i class="input-group-text border-0 mdi mdi-magnify"></i>
            </div>
            <input type="text" class="form-control bg-transparent border-0" placeholder="搜索内容">
          </div>
        </form>
      </div> -->
      <ul class="navbar-nav navbar-nav-right">
        <li class="nav-item nav-profile dropdown">
          <a class="nav-link dropdown-toggle" id="profileDropdown" href="#" data-toggle="dropdown" aria-expanded="false">
            <div class="nav-profile-img">
              <img src="<?php echo e(session('admin')->avatar); ?>" alt="image">
              <span class="availability-status online"></span>
            </div>
            <div class="nav-profile-text">
              <p class="mb-1 text-black"><?php echo e(session('admin')->account); ?></p>
            </div>
          </a>
          <div class="dropdown-menu navbar-dropdown" aria-labelledby="profileDropdown">
            <a class="dropdown-item" onclick="editInfo(<?php echo e(session('admin')->id); ?>)" href="javascript:;">
              <i class="mdi mdi-border-color mr-2 text-success"></i>
              修改信息
            </a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="/logout">
              <i class="mdi mdi-logout mr-2 text-primary"></i>
              退出登录
            </a>
          </div>
        </li>
        <li class="nav-item d-none d-lg-block full-screen-link">
          <a class="nav-link">
            <i class="mdi mdi-fullscreen" id="fullscreen-button"></i>
          </a>
        </li>
        <!-- <li class="nav-item dropdown">
          <a class="nav-link count-indicator dropdown-toggle" id="messageDropdown" href="#" data-toggle="dropdown" aria-expanded="false">
            <i class="mdi mdi-email-outline"></i>
            <span class="count-symbol bg-warning"></span>
          </a>
          <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list" aria-labelledby="messageDropdown">
            <h6 class="p-3 mb-0">未读来信</h6>


            <div class="dropdown-divider"></div>
            <a class="dropdown-item preview-item">
              <div class="preview-thumbnail">
                <img src="/assets/images/faces/face4.jpg" alt="image" class="profile-pic">
              </div>
              <div class="preview-item-content d-flex align-items-start flex-column justify-content-center">
                <h6 class="preview-subject ellipsis mb-1 font-weight-normal">Mark send you a message</h6>
                <p class="text-gray mb-0">
                  1 Minutes ago
                </p>
              </div>
            </a>


            <div class="dropdown-divider"></div>
            <h6 class="p-3 mb-0 text-center">查看全部</h6>
          </div>
        </li>


        <li class="nav-item dropdown">
          <a class="nav-link count-indicator dropdown-toggle" id="notificationDropdown" href="#" data-toggle="dropdown">
            <i class="mdi mdi-bell-outline"></i>
            <span class="count-symbol bg-danger"></span>
          </a>
          <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list" aria-labelledby="notificationDropdown">
            <h6 class="p-3 mb-0">系统消息</h6>


            <div class="dropdown-divider"></div>
            <a class="dropdown-item preview-item">
              <div class="preview-thumbnail">
                <div class="preview-icon bg-success">
                  <i class="mdi mdi-calendar"></i>
                </div>
              </div>
              <div class="preview-item-content d-flex align-items-start flex-column justify-content-center">
                <h6 class="preview-subject font-weight-normal mb-1">Event today</h6>
                <p class="text-gray ellipsis mb-0">
                  Just a reminder that you have an event today
                </p>
              </div>
            </a>


            <div class="dropdown-divider"></div>
            <h6 class="p-3 mb-0 text-center">查看全部</h6>
          </div>
        </li> -->


        
        
          
            
          
        
        
        
        
        
        
      </ul>
      <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
        <span class="mdi mdi-menu"></span>
      </button>
    </div>
  </nav>
  <!-- partial -->
  <div class="container-fluid page-body-wrapper">
    <!-- partial:partials/_sidebar.html -->
    <nav class="sidebar sidebar-offcanvas" id="sidebar">
      <ul class="nav">
        <li class="nav-item nav-profile">
          <a href="#" class="nav-link">
            <div class="nav-profile-image">
              <img src="<?php echo e(session('admin')->avatar); ?>" alt="profile">
              <span class="login-status online"></span>
            </div>
            <div class="nav-profile-text d-flex flex-column">
              <span class="font-weight-bold mb-2"><?php echo e(session('admin')->account); ?></span>
              <span class="text-secondary text-small"><?php echo e(session('admin')->nickname); ?></span>
            </div>
            <i class="mdi mdi-bookmark-check text-success nav-profile-badge"></i>
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link" href="/">
            <span class="menu-title">控制台</span>
            <i class="mdi mdi-home menu-icon"></i>
          </a>
        </li>
        


         <!-- <li class="nav-item">
          <a class="nav-link" data-toggle="collapse" href="#system-pages" aria-expanded="false" aria-controls="general-pages">
            <span class="menu-title">控制台</span>
            <i class="menu-arrow"></i>
            <i class="mdi mdi-home menu-icon"></i>
          </a>
          <div class="collapse" id="system-pages">
            <ul class="nav flex-column sub-menu">
              <li class="nav-item"> <a class="nav-link" target="main" href="/console">工具音乐控制台</a></li>
              <li class="nav-item"> <a class="nav-link" target="main" href="/admin/index/upltv">游戏控制台</a></li>
            </ul>
          </div>
        </li>
 -->



        <!-- <?php if(session('admin')->id == 1 ): ?>
        <li class="nav-item">
          <a class="nav-link" target="main" href="/admin/config/list">
            <span class="menu-title">配置项</span>
            <i class="mdi mdi-settings-box menu-icon"></i>
          </a>
        </li>
        <?php endif; ?> -->
        <?php $__currentLoopData = $menu; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k=>$v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <?php if($v->has_child): ?>
                  <li class="nav-item">
                      <a class="nav-link" data-toggle="collapse" href="#system-pages-<?php echo e($v->id); ?>" aria-expanded="false" aria-controls="general-pages">
                          <span class="menu-title"><?php echo e($v->name); ?></span>
                          <i class="menu-arrow"></i>
                          <i class="<?php echo e($v->icon); ?> menu-icon"></i>
                      </a>
                      <div class="collapse" id="system-pages-<?php echo e($v->id); ?>">
                          <ul class="nav flex-column sub-menu">
                              <?php $__currentLoopData = $v->child; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                              <li class="nav-item"> <a class="nav-link" target="main" href="<?php echo e($val->url); ?>"><?php echo e($val->name); ?></a></li>
                              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                          </ul>
                      </div>
                  </li>
          <?php else: ?>
                  <li class="nav-item">
                      <a class="nav-link" target="main" href="<?php echo e($v->url); ?>">
                          <span class="menu-title"><?php echo e($v->name); ?></span>
                          <i class="<?php echo e($v->icon); ?> menu-icon"></i>
                      </a>
                  </li>
          <?php endif; ?>

        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          <?php if(session('admin')->id == 1 ): ?>
          <li class="nav-item">
          <a class="nav-link" data-toggle="collapse" href="#system-pages1" aria-expanded="false" aria-controls="general-pages">
            <span class="menu-title">系统设置</span>
            <i class="menu-arrow"></i>
            <i class="mdi mdi-settings menu-icon"></i>
          </a>
          <div class="collapse" id="system-pages1">
            <ul class="nav flex-column sub-menu">
              <li class="nav-item"> <a class="nav-link" target="main" href="/admin/menu/list">菜单</a></li>
              <li class="nav-item"> <a class="nav-link" target="main" href="/admin/permission/list">权限</a></li>
              <li class="nav-item"> <a class="nav-link" target="main" href="/admin/role/list">角色</a></li>
              <li class="nav-item"> <a class="nav-link" target="main" href="/admin/administrator/list">用户管理</a></li>
            </ul>
          </div>
        </li>
          <?php endif; ?>

        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
      </ul>
    </nav>

    <iframe id="mainiframe" name="main" width="100%"  src="<?php echo e(url('console')); ?>"  frameborder="0"  scrolling="auto" marginheight="0" marginwidth="0"></iframe>
    <!-- main-panel ends -->
  </div>
  <!-- page-body-wrapper ends -->
</div>
<!-- container-scroller -->
<script src="https://code.jquery.com/jquery-3.0.0.min.js"></script>
<script src="/assets/layer/layer.js"></script>

<script>
   function editInfo(id) {
       var page = layer.open({
           type: 2,
           title: '修改个人信息',
           shadeClose: true,
           shade: 0.8,
           area: ['70%', '90%'],
           content: '/edit/info/'+id
       });
   }
</script>
<!-- plugins:js -->
<script src="/assets/vendors/js/vendor.bundle.base.js"></script>
<script src="/assets/vendors/js/vendor.bundle.addons.js"></script>
<!-- endinject -->
<!-- Plugin js for this page-->
<!-- End plugin js for this page-->
<!-- inject:js -->
<script src="/assets/js/off-canvas.js"></script>
<script src="/assets/js/misc.js"></script>
<!-- endinject -->
<!-- Custom js for this page-->
<script src="/assets/js/dashboard.js"></script>
<!-- End custom js for this page-->


</body>

</html>