<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta content="initial-scale=1.0,user-scalable=no,maximum-scale=1,width=device-width" name="viewport">
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <title>管理控制台</title>
  <!-- plugins:css -->
  <link rel="stylesheet" href="/assets/vendors/iconfonts/mdi/css/materialdesignicons.min.css">
  <link rel="stylesheet" href="/assets/vendors/css/vendor.bundle.base.css">
  <link rel="stylesheet" type="text/css" href="/assets/wangEditor/dist/css/wangEditor.min.css">
  <!-- endinject -->
  <!-- inject:css -->
  <link rel="stylesheet" href="/assets/css/style.css">
  <!-- endinject -->
  <link rel="shortcut icon" href="/assets/images/favicon.png" />

  <script src="https://code.jquery.com/jquery-3.0.0.min.js"></script>
  {{--datetimer--}}
  <link rel="stylesheet" id=cal_style type="text/css" href="/assets/datetimer/dist/flatpickr.min.css">
 
  <script src="https://unpkg.com/bootstrap-table@1.14.1/dist/bootstrap-table.min.js"></script>
  <script src="/assets/datetimer/src/flatpickr.js"></script>
  <script src="/assets/datetimer/src/flatpickr.l10n.zh.js"></script>
  <link href="https://unpkg.com/bootstrap-table@1.15.3/dist/bootstrap-table.min.css" rel="stylesheet">

  <script src="https://unpkg.com/bootstrap-table@1.15.3/dist/bootstrap-table.min.js"></script>
  <style>
    /*定义滚动条高宽及背景 高宽分别对应横竖滚动条的尺寸*/
    ::-webkit-scrollbar
    {
      width: 5px;
      height: 20px;
      background-color: #F5F5F5;
   
    }

    /*定义滚动条轨道 内阴影+圆角*/
    ::-webkit-scrollbar-track
    {
      -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3);
      border-radius: 3px;
      background-color: #F5F5F5;
    }

    /*定义滑块 内阴影+圆角*/
    ::-webkit-scrollbar-thumb
    {
      border-radius: 3px;
      -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,.3);
      background-color: #b66dff;
    }
  </style>
  
</head>
<body>

<script src="/assets/layer/layer.js"></script>
<script src="/assets/wangEditor/dist/js/wangEditor.min.js"></script>
@yield('base')
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
<script src="/assets/js/common.js"></script>
  <!-- End custom js for this page-->

<!--
<script type="text/javascript"> 
    var userAgent = navigator.userAgent.toLowerCase(); 
    var platform; 
    if(userAgent == null || userAgent == ''){
        platform = 'WEB' ;
    }else{
         if(userAgent.indexOf("android") != -1 ){
             platform = 'ANDROID';
             location.href = "/mobile";
         }else if(userAgent.indexOf("ios") != -1 || userAgent.indexOf("iphone") != -1 || userAgent.indexOf("ipad") != -1){
             platform = 'IOS';
             location.href = "/mobile";
         }
  }
  </script>
-->

</body>
   
</html>
