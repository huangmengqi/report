<!DOCTYPE html>
<html>

<head>
    <title>移动端报表</title>
    <link href="css/fixed_table_rc.css" type="text/css" rel="stylesheet" media="all" />
    <script src="js/jquery.min.js" type="text/javascript"></script>
    <script src="js/sortable_table.js" type="text/javascript"></script>
    <script src="js/fixed_table_rc.js" type="text/javascript"></script>






 <link rel="stylesheet" href="/assets/vendors/iconfonts/mdi/css/materialdesignicons.min.css">
  <link rel="stylesheet" href="/assets/vendors/css/vendor.bundle.base.css">
  <link rel="stylesheet" type="text/css" href="/assets/wangEditor/dist/css/wangEditor.min.css">
  <!-- endinject -->
  <!-- inject:css -->
  
  <!-- endinject -->
  <link rel="shortcut icon" href="/assets/images/favicon.png" />

  
  <link rel="stylesheet" id=cal_style type="text/css" href="/assets/datetimer/dist/flatpickr.min.css">
 

  <link href="https://unpkg.com/bootstrap-table@1.15.3/dist/bootstrap-table.min.css" rel="stylesheet">

 



    <style>
        html, body {
            font-family: Arial,鈥嬧€媠ans-serif;
            font-size: 10px;
            margin: 0;
            padding: 0;
            background-color: #f2f2f2;
        }
        div.container {
            padding: 40px 120px; /*x8*/
        }
        
        .ft_container table tr th {
            background-color: #DBEAF9;
            
        }
        .dwrapper tr th{
        height:30px;
        text-align: center;
}
    </style>
    <script>
    $(function () {
    /*    $('#fixed_hdr2').fxdHdrCol({
            fixedCols: 2,
            width: "100%",
            height: 400,
            colModal: [
            { width: 50, align: 'center' },
            { width: 110, align: 'center' },
            { width: 170, align: 'left' },
            { width: 250, align: 'left' },
            { width: 100, align: 'left' },
            { width: 70, align: 'left' },
            { width: 100, align: 'left' },
            { width: 100, align: 'center' },
            { width: 90, align: 'left' },
            { width: 400, align: 'left' }
            ]
        });                    */                          
        $('#fixed_hdr3').fxdHdrCol({
            width: "100%",//x2
            height: 1050,//x2.5
            colModal: [{width: 52, align: 'center'},
            {width: 52, align: 'center'},
            {width: 52, align: 'center'},
            {width: 52, align: 'center'},
            {width: 52, align: 'center'},
            {width: 52, align: 'center'}
            ],
            sort: true
        }); 
    });
    </script>
</head>
<body>

<style>

    .red{
        background-color:#FF3333;
    }
    .green{
        background-color:#99FF33;
    }

    .red1{
        color:#FF3333;
    }

    
    .scroll-box{width: 100%;height: 350px;overflow: auto;overflow-x: scroll;}
    .scroll-box tr{width: 100%;height: 30px;}
    .scroll-box tr td{padding: 5px;}
    

</style>




<div>
<h2>Report</h2>
<div class="card-body">
    <div class="col-lg-3" >
                <form class="forms-sample">
                    <div class="form-group" style="width:20rem">
                        
                        <div class="input-group col-xs-3">
                            <input type="text" style="width: 6rem;height: calc(2.875rem );margin-right: 10px"  class="form-control required calendar1" <?php if($time1): ?> value="<?php echo e($time1); ?>" <?php endif; ?> name="time1" placeholder="开始时间">
                            <input type="text" style="width: 6rem;height: calc(2.875rem );margin-right: 10px"  class="form-control required calendar2" <?php if($time2): ?> value="<?php echo e($time2); ?>" <?php endif; ?> name="time2" placeholder="结束时间">
                            <select class="form-control form-control-lg" name="appid" style="width: 12rem;height: calc(2.875rem);margin-right:10px">
                                    <option value="">Select App</option>
                                <?php $__currentLoopData = $gamelist; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k=>$v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if($v->status == 0): ?>
                                    <option  <?php if($appid==$v->id): ?> selected <?php endif; ?> value="<?php echo e($v->id); ?>" class="red1"><?php echo e($v->name); ?>(下架)</option>
                                    <?php else: ?>
                                    <option  <?php if($appid==$v->id): ?> selected <?php endif; ?> value="<?php echo e($v->id); ?>"><?php echo e($v->name); ?></option>
                                    <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <span class="input-group-append" >
                                <button type="submit" class=" btn btn-sm btn-gradient-primary"><i class="mdi mdi-account-search btn-icon-prepend"></i>
                                    搜索
                                </button>
                            </span>
                        </div>
                    </div>
                </form>
            </div>


 
<div class="dwrapper">
    <div class="scroll-box">

    <table id="fixed_hdr3">
        
    <thead>
    <tr><th>Date</th><th>Name</th><th>Profit</th><th>Spent</th><th>Revenue</th><th>ROI</th></tr>    

    <tbody>
        
        <?php $spe=0; $rev=0; $ro=0; $sum=0;?>

        <?php $__currentLoopData = $list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k=>$v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr>
            <?php if($appid): ?> <td><?php echo date('Y-m-d',$v->date);?></td> <?php else: ?> <td><?php echo date('m-d',strtotime($time1)).'~'.date('m-d',strtotime($time2));?></td> <?php endif; ?>
            <td><?php echo e($v->name); ?></td>
            <?php 
                $total = $v->fb_ad_network_revenue+$v->is_revenue+$v->mp_revenue+$v->al_revenue+$v->am_revenue; 
                
            ?>
            <td><?php echo round($total-$v->cost,2); ?></td>
            
            <td><?php echo e(isset($v->cost) ?$v->cost : '0.00'); ?></td>
            
            <td>
                <?php echo $total; ?>
            </td>
            <?php 
                if($total!=0&&$v->cost!=0){
                    $roi = round($total/$v->cost,2)*100;
                }else{
                    $roi = 0; 
                } 
            ?> 
            <?php if($roi <= 100): ?> 
                <td class="red"><?php echo $roi.'%';?></td>
            <?php else: ?>
                <td class="green"><?php echo $roi.'%';?></td>
            <?php endif; ?>

            <?php  $spe=$v->cost+$spe; ?>
            <?php  $rev=$total+$rev; ?>
            <?php  $ro=$roi+$ro; $sum=$sum+1;?>
        </tr>

        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php $pro=$rev-$spe; ?> 
        <tbody>  
            <tr><th>Total</th><th>---</th><th><?php echo $pro; ?></th><th><?php echo $spe; ?></th><th><?php echo $rev; ?></th><th><?php echo round($ro/$sum,2).'%'; ?></th></tr>
        </tbody>
           
    </tbody>

    </thead>
  
    </table>

</div>
    </div>
            <div class="box-footer clearfix">
                总共 <b><?php echo e($list->total()); ?></b> 条,分为<b><?php echo e($list->lastPage()); ?></b>页
                <?php echo $list->links(); ?>

            </div>
</div>
</div>
</div>
<!--
<script type="text/javascript"> 
     var url = window.location.pathname;
     var pcurl="/mobile";
     if(/AppleWebKit.*Mobile/i.test(navigator.userAgent)==false || (/MIDP|SymbianOS|NOKIA|SAMSUNG|LG|NEC|TCL|Alcatel|BIRD|DBTEL|Dopod|PHILIPS|HAIER|LENOVO|MOT-|Nokia|SonyEricsson|SIE-|Amoi|ZTE/.test(navigator.userAgent))==false){ 
         if(window.location.href.indexOf("?mobile")<0){ 
             try{ 
              if(/Android|webOS|iPhone|iPod|BlackBerry/i.test(navigator.userAgent)==false){ 
                  window.location.href=pcurl;
              } 
         }catch(e){}
      }
    }
</script>
-->
</body>

</html>