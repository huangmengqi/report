<?php $__env->startSection('base'); ?>
<div class="main-panel">
    <div class="page-header">
        <h3 class="page-title">
            <a href="javascript:location.replace(location.href);" title="refresh">
                <span class="page-title-icon bg-gradient-primary text-white mr-2">
                    <i style="color:white"class="mdi mdi-cached"></i>
                </span>
            </a>
            App Trend Line Chart
        </h3>
       
    </div>
    <div>
        <div class="col-lg-3" style="float: right">
                <form class="forms-sample">
                    <div class="form-group" style="width:100%">
                        <div class="input-group col-xs-3">
                            <input type="text" style="width: 25%;height: calc(2.875rem );margin-right: 10px"  class="form-control required calendar1" name="time1" <?php if($time1): ?> value="<?php echo e($time1); ?>" <?php endif; ?> placeholder="开始时间">
                            <input type="text" style="width: 25%;height: calc(2.875rem );margin-right: 10px"  class="form-control required calendar2" name="time2" <?php if($time2): ?> value="<?php echo e($time2); ?>" <?php endif; ?> placeholder="结束时间">
                            <span class="input-group-append" >
                                <button type="submit" class=" btn btn-sm btn-gradient-primary"><i class="mdi mdi-account-search btn-icon-prepend"></i>
                                    搜索
                                </button>
                            </span>
                        </div>
                    </div>
                </form>
            </div>
    </div>

    <script src='/assets/js/echarts.js'></script>

    <input id="array1" value="<?php echo e(json_encode($apptotalprofit,TRUE)); ?>" type="hidden">


    <?php $__currentLoopData = $apptotalprofit; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k=>$v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div style="padding-top: 20px">
            <input id="array" value="<?php echo e(json_encode($v,TRUE)); ?>" type="hidden">
            <?php $__currentLoopData = $gamelist; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k1 => $v1): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if($k1==$k): ?> <h3 style="text-align:center;">App Line Chart---<?php echo e($v1->name); ?></h3>  <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <div id="main_<?php echo e($k); ?>" style="width: 100%;height:300px;padding-top:10px"></div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

</div>

<script type="text/javascript">
        $(".calendar1").flatpickr({
           dateFormat:"Y-m-d",
           enableTime:false,
           maxDate: "today"
           //更多配置百度搜索flatpickr即可找到
        });
        $(".calendar2").flatpickr({
           dateFormat:"Y-m-d",
           enableTime:false,
           maxDate: "today"
           //更多配置百度搜索flatpickr即可找到
        });
        $(document).ready(function(){
            var obj = JSON.parse(document.getElementById('array1').value);
            for(var i=0;i<obj.length;i++){
                // 基于准备好的dom，初始化echarts实例
                var barChart_show = echarts.init(document.getElementById('main_'+i));
                 // 指定图表的配置项和数据              
                var bar_option_show = {
                    color: ['#3398DB'],
                    tooltip: {
                        trigger: 'axis',
                    },
                    legend: {
                        data:['Profit']
                    },
                    grid: {  
                        bottom:'10%'//显示数据距离X轴的高度
                    },
                    xAxis: {
                        type: 'category',
                        boundaryGap: false,
                        data: <?php echo $datearray;?>
                    },
                    
                    yAxis: {
                        type: 'value',
                    },
                    series: [{
                        name: 'Profit',
                        type: 'line',
                        data:obj[i]
                    }]
                };
                // 使用刚指定的配置项和数据显示图表。
                barChart_show.setOption(bar_option_show);

            }
        }) 
    </script>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('base.base', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>