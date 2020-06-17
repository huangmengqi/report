
<?php $__env->startSection('base'); ?>
<style type="text/css">
    .today td{
        color:#46c35f;
    }
    .total td{
        color:#E91E63;
        padding: 10px;
    }
    .red{
        color:#FF3333;
    }
</style>
    <!-- 内容区域 -->
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="page-header">
                <h3 class="page-title">
                    <a href="javascript:location.replace(location.href);" title="refresh">
                        <span class="page-title-icon bg-gradient-primary text-white mr-2">
                            <i style="color:white"class="mdi mdi-cached"></i>
                        </span>
                    </a>
                    Daily Report
                </h3>
                <nav aria-label="breadcrumb">
                    <p class="card-description">
                        <a href="/admin/report/downloadmutiday?appid=<?php echo e($appid); ?>&date=<?php echo e($time1); ?>&today=<?php echo e($time2); ?>&gamename=<?php echo e($gamename); ?>" class="btn btn-sm btn-gradient-success btn-icon-text">Export Muti Days Excel</a>
                    </p>
                </nav>
            </div>
            <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="col-lg-3">
                                <form class="forms-sample">
                                    <div class="form-group" style="width: 520px">
                                        
                                        <div class="input-group col-xs-3">
                                            <input type="text" style="width: 110px;height: calc(2.875rem + 2px);margin-right: 20px"  class="form-control required calendar1" <?php if($time1): ?> value="<?php echo e($time1); ?>" <?php endif; ?> name="time1" placeholder="开始时间">
                                            <input type="text" style="width: 110px;height: calc(2.875rem + 2px);margin-right: 20px"  class="form-control calendar2" <?php if($time2): ?> value="<?php echo e($time2); ?>" <?php endif; ?> name="time2" placeholder="结束时间">
                                            <select class="form-control form-control-lg" name="appid" style="width: 150px;height: calc(2.875rem + 2px);margin-right: 20px">
                                                <?php $__currentLoopData = $gamelist; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k=>$v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option  <?php if($appid==$v->id): ?> selected <?php endif; ?> value="<?php echo e($v->id); ?>"><?php echo e($v->name); ?></option>
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
                                <div <?php if($appid != 143 && $appid != 150): ?> style="display: none" <?php endif; ?>>
                                    <form class="forms-sample">
                                        <div class="form-group" style="width: 500px">
                                            <div class="input-group col-xs-3">
                                                <input type="text" style="width: 50px;height: calc(1.875rem + 2px);margin-right: 20px"  required="required" class="form-control calendar3  required" name="time3" placeholder="选择时间">
                                                
                                                <input type="text" style="width: 0px;height: calc(1.875rem + 2px);margin-right: 20px"  required="required" class="form-control"  name="cost" placeholder="Spend">
                                                
                                                <input type="text" style="width: 0px;height: calc(1.875rem + 2px);margin-right: 20px"   required="required" class="form-control" name="install" placeholder="Install">
                                                
                                                <button type="submit" class=" btn btn-sm btn-gradient-primary"><i class="mdi mdi-account-search btn-icon-prepend" ></i>
                                                    添加
                                                </button>   
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                

                            </div>
                            <table class="table table-bordered" id="form-table1">
                                <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Profit</th>
                                    <th>ROI</th>
                                    <th>Installs</th>
                                    <th>Cost per Intall</th>
                                    <th>Facebook Spent</th>
                                    <th>FAN Revenue</th>
                                    <th>IronSoure Revenue</th>
                                    <th>Mopub Revenue</th>
                                    <th>Applovin Revenue</th>
                                    <th>Admob Revenue</th>
                                    <th>Revenue</th>
                                </tr>
                                <tr id='totalRow' class="total"></tr>
                                </thead>
                                <tbody id="tb">
                                <?php $__currentLoopData = $list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k=>$v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo date('Y-m-d',$v->date); ?></td>
                                        <td><?php $total = $v->fb_ad_network_revenue+$v->is_revenue+$v->mp_revenue+$v->al_revenue+$v->am_revenue+$v->upltv; echo round($total-$v->cost,2); ?></td>
                                        
                                        <td>
                                            <?php 
                                            if($total!=0&&$v->cost!=0){
                                                echo (round($total/$v->cost,2)*100).'%';
                                            }else{
                                                echo '0'; 
                                            }
                                            ?>
                                        </td>
                                        <td><?php echo e(isset($v->install_count) ?$v->install_count : '0'); ?></td>
                                        <td>
                                            <?php 
                                            if($v->install_count!=0&&$v->cost!=0){
                                                echo round($v->cost/$v->install_count,2); 
                                            }else{
                                                echo 0.00; 
                                            }
                                            ?>
                                        </td>
                                        <td><?php echo e(isset($v->cost) ?$v->cost : '0.00'); ?></td>
                                        <td><?php echo e(isset($v->fb_ad_network_revenue) ?$v->fb_ad_network_revenue+$v->upltv : '0.00'); ?></td>
                                        <td><?php echo e(isset($v->is_revenue) ?$v->is_revenue : '0.00'); ?></td>
                                        <td><?php echo e(isset($v->mp_revenue) ?$v->mp_revenue : '0.00'); ?></td>
                                        <td><?php echo e(isset($v->al_revenue) ?$v->al_revenue : '0.00'); ?></td>
                                        <td><?php echo e(isset($v->am_revenue) ?$v->am_revenue : '0.00'); ?></td>
                                        <td>
                                            <?php echo $total; ?>
                                        </td>
                                        
                                        
                                        
                                        
                                        <!-- <td>77</td> -->
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                            <div class="box-footer clearfix">
                                总共 <b><?php echo e($list->total()); ?></b> 条,分为<b><?php echo e($list->lastPage()); ?></b>页
                                <?php echo $list->links(); ?>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script>
        
        $(".calendar1").flatpickr({
           dateFormat:"Y-m-d",
           enableTime:false
           //更多配置百度搜索flatpickr即可找到
       });
        $(".calendar2").flatpickr({
           dateFormat:"Y-m-d",
           enableTime:false,
           maxDate: "today"
           //更多配置百度搜索flatpickr即可找到
       });
        $(".calendar3").flatpickr({
           dateFormat:"Y-m-d",
           enableTime:false,
           maxDate: "today"
           //更多配置百度搜索flatpickr即可找到
       });
        
        function del(id){
            myConfirm("删除操作不可逆,是否继续?",function(){
                myRequest("/admin/administrator/del/"+id,"post",{},function(res){
                    layer.msg(res.msg)
                    setTimeout(function(){
                        window.location.reload();
                    },1500)
                });
            });
        }

    </script>
<script type="text/javascript">
    $(document).ready(function(){ 
        var totalRow1=0 
      $('#form-table1 tbody tr').each(function(){ 
        $(this).find('td:eq(1)').each(function(){ 
        totalRow1 += parseFloat($(this).text()); 
      }); 
    }); 
      var totalRow2=0 
      $('#form-table1 tbody tr').each(function(){ 
        $(this).find('td:eq(2)').each(function(){ 
        totalRow2 += parseFloat($(this).text()); 
      }); 
    }); 
      var totalRow3=0 
      $('#form-table1 tbody tr').each(function(){ 
        $(this).find('td:eq(3)').each(function(){ 
        totalRow3 += parseFloat($(this).text()); 
      }); 
    }); 
      var totalRow4=0 
      $('#form-table1 tbody tr').each(function(){ 
        $(this).find('td:eq(4)').each(function(){ 
        totalRow4 += parseFloat($(this).text()); 
      }); 
    }); 
      var totalRow5=0 
      $('#form-table1 tbody tr').each(function(){ 
        $(this).find('td:eq(5)').each(function(){ 
        totalRow5 += parseFloat($(this).text()); 
      }); 
    }); 
      var totalRow6=0 
      $('#form-table1 tbody tr').each(function(){ 
        $(this).find('td:eq(6)').each(function(){ 
        totalRow6 += parseFloat($(this).text()); 
      }); 
    }); 
      var totalRow7=0 
      $('#form-table1 tbody tr').each(function(){ 
        $(this).find('td:eq(7)').each(function(){ 
        totalRow7 += parseFloat($(this).text()); 
      }); 
    }); 
      var totalRow8=0 
      $('#form-table1 tbody tr').each(function(){ 
        $(this).find('td:eq(8)').each(function(){ 
        totalRow8 += parseFloat($(this).text()); 
      }); 
    }); 
      var totalRow9=0 
      $('#form-table1 tbody tr').each(function(){ 
        $(this).find('td:eq(9)').each(function(){ 
        totalRow9+= parseFloat($(this).text()); 
      }); 
    }); 
      var totalRow10=0 
      $('#form-table1 tbody tr').each(function(){ 
        $(this).find('td:eq(10)').each(function(){ 
        totalRow10 += parseFloat($(this).text()); 
      }); 
    }); 
      var totalRow11=0 
      $('#form-table1 tbody tr').each(function(){ 
        $(this).find('td:eq(11)').each(function(){ 
        totalRow11 += parseFloat($(this).text()); 
      }); 
    }); 
    $('#totalRow').append('<td>Total</td><td>'+totalRow1.toFixed(2)+'</td><td>'+(totalRow11/totalRow5).toFixed(2)*100+'%'+'</td><td>'+totalRow3+'</td><td>'+(totalRow5/totalRow3).toFixed(2)+'</td><td>'+totalRow5.toFixed(2)+'</td><td>'+totalRow6.toFixed(2)+'</td><td>'+totalRow7.toFixed(2)+'</td><td>'+totalRow8.toFixed(2)+'</td><td>'+totalRow9.toFixed(2)+'</td><td>'+totalRow10.toFixed(2)+'</td><td>'+totalRow11.toFixed(2)+'</td>'); 
    }); 
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('base.base', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>