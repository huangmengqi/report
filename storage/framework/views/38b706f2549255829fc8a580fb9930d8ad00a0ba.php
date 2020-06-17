<?php $__env->startSection('base'); ?>

<div class="main-panel">
    <div class="page-header">
        <h3 class="page-title">
            <a href="javascript:location.replace(location.href);" title="refresh">
                <span class="page-title-icon bg-gradient-primary text-white mr-2">
                    <i style="color:white"class="mdi mdi-cached"></i>
                </span>
            </a>
            Trend Line Chart
        </h3>
       
    </div>

    <div>
        <h3 style="text-align:center;padding-bottom: 2px">Total Report</h3>
        <div class="col-lg-3" style="float: right">
                <form class="forms-sample">
                    <div class="form-group" style="width:100%">
                        <div class="input-group col-xs-3">
                            <input type="text" style="width: 25%;height: calc(2.875rem );margin-right: 10px"  class="form-control required calendar1" name="time1" <?php if($time1): ?> value="<?php echo e($time1); ?>" <?php endif; ?> placeholder="开始时间">
                            <input type="text" style="width: 25%;height: calc(2.875rem );margin-right: 10px"  class="form-control required calendar2" name="time2" <?php if($time2): ?> value="<?php echo e($time2); ?>" <?php endif; ?> placeholder="结束时间">
                            <select class="form-control form-control-lg" name="appid" style="width: 15%;height: calc(2.875rem + 2px);margin-right: 20px">
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
            </div>
    </div>



    <script src='/assets/js/echarts.js'></script>
    <div id="total" style="width: 100%;height:400px;"></div>
    <div style="padding-top: 20px">
        <h3 style="text-align:center;">App Report----<?php echo e($appname); ?></h3>
        <div id="main" style="width: 100%;height:300px;padding-top:10px"></div>
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
        // 基于准备好的dom，初始化echarts实例
        var total = echarts.init(document.getElementById('total'));

        option1 = {
            tooltip: {
                trigger: 'axis'
            },
            legend: {
                data:['Profit','Admob','FAN','Spend','Revenue','Roi']
            },
            grid: {
                left: '3%',
                right: '4%',
                bottom: '3%',
                containLabel: true
            },
            xAxis: {
                type: 'category',
                boundaryGap: false,
                data: <?php echo $datearray;?>
            },
            yAxis: {
                type: 'value',
                        
                        

            },
            series: [
                {
                    name:'Profit',
                    type:'line',
                    data:<?php echo $profit;?>
                },
                {
                    name:'Admob',
                    type:'line',
                    data:<?php echo $am;?>
                },
                {
                    name:'FAN',
                    type:'line',
                    data:<?php echo $fb;?>
                },
                {
                    name:'Spend',
                    type:'line',
                    data:<?php echo $spend;?>
                },
                {
                    name:'Revenue',
                    type:'line',
                    data:<?php echo $revenue;?>
                },
                {
                    name:'Roi',
                    type:'line',
                    data:<?php echo $roi;?>
                }
            ]
        };

        // 使用刚指定的配置项和数据显示图表。
        total.setOption(option1);
    </script>




    
    <script type="text/javascript">
       
        // 基于准备好的dom，初始化echarts实例
        var myChart = echarts.init(document.getElementById('main'));

        option = {
            tooltip: {
                trigger: 'axis'
            },
            legend: {
                data:['Profit','Admob','FAN','Spend','Revenue','Roi']
            },
            grid: {
                left: '3%',
                right: '4%',
                bottom: '3%',
                containLabel: true
            },
            xAxis: {
                type: 'category',
                boundaryGap: false,
                data: <?php echo $datearray;?>
            },
            yAxis: {
                type: 'value'
            },
            series: [
                {
                    name:'Profit',
                    type:'line',
                    data:<?php echo $appprofit;?>
                },
                {
                    name:'Admob',
                    type:'line',
                    data:<?php echo $appam;?>
                },
                {
                    name:'FAN',
                    type:'line',
                    data:<?php echo $appfb;?>
                },
                {
                    name:'Spend',
                    type:'line',
                    data:<?php echo $appspend;?>
                },
                {
                    name:'Revenue',
                    type:'line',
                    data:<?php echo $apprevenue;?>
                },
                {
                    name:'Roi',
                    type:'line',
                    data:<?php echo $approi;?>
                }
            ]
        };

        // 使用刚指定的配置项和数据显示图表。
        myChart.setOption(option);
    </script>
    



</div>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('base.base', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>