
<?php $__env->startSection('base'); ?>

<style>
    #tableSort {
        moz-user-select: -moz-none;
        -moz-user-select: none;
        -o-user-select: none;
        -khtml-user-select: none;
        -webkit-user-select: none;
        -ms-user-select: none;
        user-select: none;
        border-collapse: collapse;
        border-spacing: 0;
        margin: 0;
        padding: 0;
        width: 100%;
        text-align: center;
        margin: 15px 0;
        overflow:auto;
        white-space:nowrap;
    }
    #tableSort th {
        cursor: pointer;
        background: #eee;
   }
    #tableSort tr:nth-child(even) {
        background: #f9f9f9
    }
    #tableSort td {
        padding: 10px;
        border: 1px solid #ccc;
    }
    .red{
        background-color:#FF3333;
    }
    .green{
        background-color:#99FF33;
    }

	.red1{
        color:#FF3333;
    }
	.scroll-box
	{
		width: 100vw;
		height: 1px;
		min-height: 100%;
		overflow-y: auto;
		_overflow: auto;
		margin: 0 0 1em;
		-webkit-overflow-scrolling: touch;
	}
	
	.scroll-box ::-webkit-scrollbar
	{
		overflow: auto;
		-webkit-appearance: none;
		width: 20px;
		height: 140px;
		-webkit-overflow-scrolling: touch;
	}
	
	.scroll-box ::-webkit-scrollbar-thumb
	{
		border-radius: 8px;
		border: 3px solid #fff;
		background-color: rgba(0, 0, 0, .3);
		-webkit-overflow-scrolling: touch;
	}
	

	#form-table{width: 100%;margin: 0 auto;text-align: center;table-layout: fixed;}
    #form-table th{background: #F3F3F3;height:2rem;font-size: 0.9rem;/*border-right: 1px #ccc solid;*/}
    #form-table1{width: 100%;margin: 0 auto;text-align: center;table-layout: fixed;}
    #form-table1 td{border:0;font-size: 0.9rem;border: 1px #ccc solid;}
    .fixed-tfoot tr td{border-bottom: 1px #ccc solid;background: #F3F3F3;}
    .fixed-thead,.fixed-tfoot{padding-right:17px;}
    .fixed-thead tr th,.fixed-tfoot tr td{height: 30px;font-size: 12px;text-align: center;}
    .scroll-box{width: 100%;height: 500px;overflow: auto;overflow-x:hidden;}
    .scroll-box tr{width: 100%;height: 40px;line-height: 20px;}
    .scroll-box tr td{padding: 5px;}
	

    #totalRow td{
        color:#E91E63;
        padding: 5px;
        border-top: 1px #ccc solid;
       /* border-right: 1px #ccc solid;
        border-left: 1px #ccc solid;*/
    }

</style>
<?php
    function isMobile() {
        // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
        if (isset($_SERVER['HTTP_X_WAP_PROFILE']))
            return true;
        // 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
        if (isset($_SERVER['HTTP_VIA'])) {
            // 找不到为flase,否则为true
            return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
        }
        // 脑残法，判断手机发送的客户端标志,兼容性有待提高
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $clientkeywords = array('nokia', 'sony', 'ericsson', 'mot', 'samsung', 'htc', 'sgh', 'lg', 'sharp', 'sie-', 'philips', 'panasonic', 'alcatel', 'lenovo', 'iphone', 'ipod', 'blackberry', 'meizu', 'android', 'netfront', 'symbian', 'ucweb', 'windowsce', 'palm', 'operamini', 'operamobi', 'openwave', 'nexusone', 'cldc', 'midp', 'wap', 'mobile');
            // 从HTTP_USER_AGENT中查找手机浏览器的关键字
            if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT'])))
                return true;
        }
        // 协议法，因为有可能不准确，放到最后判断
        if (isset($_SERVER['HTTP_ACCEPT'])) {
            // 如果只支持wml并且不支持html那一定是移动设备
            // 如果支持wml和html但是wml在html之前则是移动设备
            if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
                return true;
            }
        }
    return false;
}
?>
<?php if(isMobile()=='true'): ?>
<div class="main-panel">
        <div class="card-body wwww" style="padding: 0.5rem;">
            <div class="col-lg-3" >
                <form class="forms-sample">
                    <div class="form-group" style="width:20rem">
                        
                        <div class="input-group col-xs-3">
                            <input type="text" style="width: 2.5rem;height: calc(2.875rem );margin-right: 10px"  class="form-control required calendar1" <?php if($time1): ?> value="<?php echo e($time1); ?>" <?php endif; ?> name="time1" placeholder="开始时间">
                            <input type="text" style="width: 2.5rem;height: calc(2.875rem );margin-right: 10px"  class="form-control required calendar2" <?php if($time2): ?> value="<?php echo e($time2); ?>" <?php endif; ?> name="time2" placeholder="结束时间">
                            <select class="form-control form-control-lg" name="appid" style="width: 2.5rem;height: calc(2.875rem);margin-right:10px">
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
                          
            <table id="form-table" cellpadding="0" border="0" cellspacing="0">
            <thead>
                <tr>
                    <th data-type="string">Name</th>
                    <th data-type="num">Profit</th>
                    <th data-type="num">Install</th>
                    <th data-type="num">Spent</th>
                    <th data-type="num">Upltv</th>
                    <th data-type="num">Revenue</th>
                    <th data-type="roi">ROI</th>
                    
                </tr>
            </thead>
            <tr id='totalRow'></tr>
            </table>
                <div class="scroll-box">
                    <table id="form-table1" cellpadding="0" cellspacing="0">
                    <tbody>
                    <?php $__currentLoopData = $list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k=>$v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            
                            <td><?php echo e($v->name); ?></td>
                            <?php 
                                $total = $v->fb_ad_network_revenue+$v->is_revenue+$v->mp_revenue+$v->al_revenue+$v->am_revenue+$v->upltv_revenue; 
                                
                            ?>
                            <td><?php echo round($total-$v->cost,0); ?></td>
                            <td><?php echo e(isset($v->install_count) ?$v->install_count : '0'); ?></td>
                            <td><?php echo e(isset($v->cost) ?round($v->cost,0) : '0.00'); ?></td>
                            <td><?php echo e(isset($v->upltv_revenue) ?round($v->upltv_revenue,0) : '0.00'); ?></td>
                            <td>
                                <?php echo round($total,0); ?>
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

                            
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
                </table>
            </div>
            <div class="box-footer clearfix">
                总共 <b><?php echo e($list->total()); ?></b> 条,分为<b><?php echo e($list->lastPage()); ?></b>页
                <?php echo $list->links(); ?>

            </div>
            
        </div>
    </div>
<script>
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
    (function () {
        var tbody = document.querySelector('#form-table1').tBodies[0];
        var th = document.querySelector('#form-table').tHead.rows[0].cells;
        // console.log(document.querySelector('#form-table').tHead.rows[0])
        var td = tbody.rows;
        for (var i = 0; i < th.length; i++) {
            th[i].flag = 1;
            th[i].onclick = function () {
                sort(this.getAttribute('data-type'), this.flag, this.cellIndex);
                this.flag = -this.flag;
            };
        };
        function sort(str, flag, n) {
            var arr = [];
            for (var i = 0; i < td.length; i++) {
                arr.push(td[i]);
            };
            arr.sort(function (a, b) {
                return method(str, a.cells[n].innerHTML, b.cells[n].innerHTML) * flag;
            });
            for (var i = 0; i < arr.length; i++) {
                tbody.appendChild(arr[i]);
            };
        };
        function method(str, a, b) {
            switch (str) {
                case 'num':
                    return b - a;
                    break;
                case 'string':
                    return a.localeCompare(b);
                    break;
                case 'roi':
                    return b.replace("%","")/100 - a.replace("%","")/100;
                    break;
                default:
                    return new Date(a.split('-').join('/')).getTime() - new Date(b.split('-').join('/')).getTime();
            };
        };
    })();
</script>
<script type="text/javascript">
    $(document).ready(function(){ 
      var totalRow2=0 
      $('#form-table1 tr').each(function(){ 
        $(this).find('td:eq(1)').each(function(){ 
        totalRow2 += parseFloat($(this).text()); 
      }); 
    }); 
      var totalRow3=0 
      $('#form-table1 tr').each(function(){ 
        $(this).find('td:eq(2)').each(function(){ 
        totalRow3 += parseFloat($(this).text()); 
      }); 
    }); 
      var totalRow4=0 
      $('#form-table1 tr').each(function(){ 
        $(this).find('td:eq(3)').each(function(){ 
        totalRow4 += parseFloat($(this).text()); 
      }); 
    }); 
      var totalRow5=0 
      $('#form-table1 tr').each(function(){ 
        $(this).find('td:eq(4)').each(function(){ 
        totalRow5 += parseFloat($(this).text()); 
      }); 
    }); 
      var totalRow6=0 
      $('#form-table1 tr').each(function(){ 
        $(this).find('td:eq(5)').each(function(){ 
        totalRow6 += parseFloat($(this).text()); 
      }); 
    }); 
      var totalRow7=0 
      $('#form-table1 tr').each(function(){ 
        $(this).find('td:eq(6)').each(function(){ 
        totalRow7 += parseFloat($(this).text()); 
      }); 
    }); 
    
    $('#totalRow').append('<td>--</td><td>'+totalRow2.toFixed(0)+'</td><td>'+totalRow3.toFixed(0)+'</td><td>'+totalRow4.toFixed(0)+'</td><td>'+totalRow5.toFixed(0)+'</td><td>'+totalRow6.toFixed(0)+'</td><td>'+((totalRow6/totalRow4)*100).toFixed(0)+'%'+'</td>'); 
    }); 
</script>
<?php else: ?>
<div class="main-panel">
        <div class="page-header">
            <h3 class="page-title">
                <a href="javascript:location.replace(location.href);" title="refresh">
                    <span class="page-title-icon bg-gradient-primary text-white mr-2">
                        <i style="color:white"class="mdi mdi-cached"></i>
                    </span>
                </a>
                Upltv Report（Only Games）
            </h3>
           
        </div>
        <div class="card-body">
            <div class="col-lg-3" >
                <form class="forms-sample">
                    <div class="form-group" style="width:20rem">
                        
                        <div class="input-group col-xs-3">
                            <input type="text" style="width: 4rem;height: calc(2.875rem );margin-right: 10px"  class="form-control required calendar1" <?php if($time1): ?> value="<?php echo e($time1); ?>" <?php endif; ?> name="time1" placeholder="开始时间">
                            <input type="text" style="width: 4rem;height: calc(2.875rem );margin-right: 10px"  class="form-control required calendar2" <?php if($time2): ?> value="<?php echo e($time2); ?>" <?php endif; ?> name="time2" placeholder="结束时间">
                            <select class="form-control form-control-lg" name="appid" style="width: 4rem;height: calc(2.875rem);margin-right:10px">
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
                          
            <table id="form-table" cellpadding="0" border="0" cellspacing="0">
            <thead>
                <tr>
                    <th>Date</th>
                    <th data-type="string">Name</th>
                    <th data-type="num">Profit</th>
                    <th data-type="num">Install</th>
                    <th data-type="num">Spent</th>
                    <th data-type="num">Upltv</th>
                    <th data-type="num">Revenue</th>
                    <th data-type="roi">ROI</th>
                    <th data-type="string">Start Date</th>
                    <th data-type="string">Operator</th>
                </tr>
            </thead>
            <tr id='totalRow'></tr>
            </table>
                <div class="scroll-box">
                    <table id="form-table1" cellpadding="0" cellspacing="0">
                    <tbody>
                    <?php $__currentLoopData = $list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k=>$v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <?php if($appid): ?> <td><?php echo date('Y-m-d',$v->date);?></td> <?php else: ?> <td><?php echo date('m-d',strtotime($time1)).'~'.date('m-d',strtotime($time2));?></td> <?php endif; ?>
                            <td><?php echo e($v->name); ?></td>
                            <?php 
                                $total = $v->fb_ad_network_revenue+$v->is_revenue+$v->mp_revenue+$v->al_revenue+$v->am_revenue+$v->upltv_revenue; 
                                
                            ?>
                            <td><?php echo round($total-$v->cost,2); ?></td>
                            <td><?php echo e(isset($v->install_count) ?$v->install_count : '0'); ?></td>
                            <td><?php echo e(isset($v->cost) ?$v->cost : '0.00'); ?></td>
                            <td><?php echo e(isset($v->upltv_revenue) ?$v->upltv_revenue : '0.00'); ?></td>
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

                            <td>
                                <?php echo date('Y-m-d',$v->add_time); ?>
                            </td>
                            <td>
                                <?php echo e($v->operator); ?>

                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
                </table>
            </div>
            <div class="box-footer clearfix">
                总共 <b><?php echo e($list->total()); ?></b> 条,分为<b><?php echo e($list->lastPage()); ?></b>页
                <?php echo $list->links(); ?>

            </div>
            
        </div>
    </div>
<script>
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
    (function () {
        var tbody = document.querySelector('#form-table1').tBodies[0];
        var th = document.querySelector('#form-table').tHead.rows[0].cells;
        // console.log(document.querySelector('#form-table').tHead.rows[0])
        var td = tbody.rows;
        for (var i = 0; i < th.length; i++) {
            th[i].flag = 1;
            th[i].onclick = function () {
                sort(this.getAttribute('data-type'), this.flag, this.cellIndex);
                this.flag = -this.flag;
            };
        };
        function sort(str, flag, n) {
            var arr = [];
            for (var i = 0; i < td.length; i++) {
                arr.push(td[i]);
            };
            arr.sort(function (a, b) {
                return method(str, a.cells[n].innerHTML, b.cells[n].innerHTML) * flag;
            });
            for (var i = 0; i < arr.length; i++) {
                tbody.appendChild(arr[i]);
            };
        };
        function method(str, a, b) {
            switch (str) {
                case 'num':
                    return b - a;
                    break;
                case 'string':
                    return a.localeCompare(b);
                    break;
                case 'roi':
                    return b.replace("%","")/100 - a.replace("%","")/100;
                    break;
                default:
                    return new Date(a.split('-').join('/')).getTime() - new Date(b.split('-').join('/')).getTime();
            };
        };
    })();
</script>
<script type="text/javascript">
    $(document).ready(function(){ 
      var totalRow2=0 
      $('#form-table1 tr').each(function(){ 
        $(this).find('td:eq(2)').each(function(){ 
        totalRow2 += parseFloat($(this).text()); 
      }); 
    }); 
      var totalRow3=0 
      $('#form-table1 tr').each(function(){ 
        $(this).find('td:eq(3)').each(function(){ 
        totalRow3 += parseFloat($(this).text()); 
      }); 
    }); 
      var totalRow4=0 
      $('#form-table1 tr').each(function(){ 
        $(this).find('td:eq(4)').each(function(){ 
        totalRow4 += parseFloat($(this).text()); 
      }); 
    }); 
      var totalRow5=0 
      $('#form-table1 tr').each(function(){ 
        $(this).find('td:eq(5)').each(function(){ 
        totalRow5 += parseFloat($(this).text()); 
      }); 
    }); 
      var totalRow6=0 
      $('#form-table1 tr').each(function(){ 
        $(this).find('td:eq(6)').each(function(){ 
        totalRow6 += parseFloat($(this).text()); 
      }); 
    }); 
      var totalRow7=0 
      $('#form-table1 tr').each(function(){ 
        $(this).find('td:eq(7)').each(function(){ 
        totalRow7 += parseFloat($(this).text()); 
      }); 
    }); 
      var totalRow8=0 
      $('#form-table1 tr').each(function(){ 
        $(this).find('td:eq(8)').each(function(){ 
        totalRow7 += parseFloat($(this).text()); 
      }); 
    }); 
      var totalRow9=0 
      $('#form-table1 tr').each(function(){ 
        $(this).find('td:eq(9)').each(function(){ 
        totalRow7 += parseFloat($(this).text()); 
      }); 
    }); 
    $('#totalRow').append('<td>Total</td><td>--</td><td>'+totalRow2.toFixed(0)+'</td><td>'+totalRow3.toFixed(0)+'</td><td>'+totalRow4.toFixed(0)+'</td><td>'+totalRow5.toFixed(0)+'</td><td>'+totalRow6.toFixed(0)+'</td><td>'+((totalRow6/totalRow4)*100).toFixed(0)+'%'+'</td><td>--</td><td>--</td>'); 
    }); 
</script>
<?php endif; ?>




<!-- <script type="text/javascript">
    var isMobile = /Android|webOS|iPhone|iPad|BlackBerry/i.test(navigator.userAgent)
    if (isMobile) {
        window.location.href="/mobile"; 
    }
 </script> 
-->

    <!-- <script type="text/javascript"> 
        var url = window.location.pathname;
        var wapurl="/mobile";
            if(/AppleWebKit.*Mobile/i.test(navigator.userAgent) || (/MIDP|SymbianOS|NOKIA|SAMSUNG|LG|NEC|TCL|Alcatel|BIRD|DBTEL|Dopod|PHILIPS|HAIER|LENOVO|MOT-|Nokia|SonyEricsson|SIE-|Amoi|ZTE/.test(navigator.userAgent))){ 
            if(window.location.href.indexOf("?mobile")<0){
                try{
                    if(/Android|webOS|iPhone|iPod|BlackBerry/i.test(navigator.userAgent)){
                           window.location.href=wapurl;
                     }else{
                           window.location.href=wapurl;
                     }
                }catch(e){}
        }
    }
    </script -->
<?php $__env->stopSection(); ?>
<?php echo $__env->make('base.base', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>