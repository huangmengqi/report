
<?php $__env->startSection('base'); ?>
<script type="text/javascript" src="http://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="http://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="http://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <style>
        *{margin:0;padding:0;}
        .container{border: 1px #ccc solid;width: 90%;height:100margin: 100px auto;}
        #form-table{width: 100%;margin: 0 auto;text-align: center;table-layout: fixed;}
        #form-table th{border-right: 1px #ccc solid;border-bottom: 1px #ccc solid;background: #F3F3F3;height:2rem;}
        #form-table td{border:0;}
        #form-table1{width: 100%;margin: 0 auto;text-align: center;table-layout: fixed;}
        #form-table1 th{border-right: 1px #ccc solid;border-bottom: 1px #ccc solid;background: #F3F3F3;}
        #form-table1 td{border:0;font-size:16px;}
        .fixed-tfoot tr td{background: #F3F3F3;}
        .fixed-thead,.fixed-tfoot{padding-right:17px;}
        .fixed-thead tr th,.fixed-tfoot tr td{height: 50px;font-size: 16px;text-align: center;}
        .scroll-box{width: 100%;height: 550px;overflow: auto;overflow-x:hidden;}
        .scroll-box tr{width: 100%;height: 40px;line-height: 20px;}
        .scroll-box tr td{padding: 5px;}
        /*.scroll-box tr:nth-child(odd) td{background-color: #ECE9D8;}*/
        .tip{margin: 0 auto;text-align: center;color: red;line-height: 150%;font-size: 14px;}
        .head td{font-size:16px;font-weight:bold;padding:15px 15px;}
        .time1 td{
            color:#46c35f;
        }
        .total td{
            color:#E91E63;
            padding: 10px;
            border-bottom: 1px #ccc solid;
            border-right: 1px #ccc solid;
            border-left: 1px #ccc solid;
        }
        .red{
            background-color:#FF3333;
        }
        .red1{
            color:#FF3333;
        }
        .green{
            background-color:#99FF33;
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
                    Country Report
                </h3>
                <nav aria-label="breadcrumb">
                    <p class="card-description">
                        <a href="/admin/report/downloadhezuo?appid=<?php echo e($appid); ?>&date=<?php echo e($time1); ?>&gamename=<?php echo e($gamename); ?>" class="btn btn-sm btn-gradient-success btn-icon-text">Export Excel</a>
                    </p>
                    <!-- <p class="card-description">
                        <a href="/admin/report/downloadmutihezuo?appid=<?php echo e($appid); ?>&date=<?php echo e($time1); ?>&today=<?php echo e($time2); ?>&gamename=<?php echo e($gamename); ?>" class="btn btn-sm btn-gradient-success btn-icon-text">Export Muti Days Excel</a>
                    </p> -->
                </nav>
            </div>
            <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">

                            <div class="col-lg-3">
                                <form class="forms-sample">
                                    <div class="form-group" style="width: 1000px">
                                        <div class="input-group col-xs-3">
<input type="text"  readonly style="width: 10rem;height: calc(2.875rem );margin-right: 20px"  class="form-control" name="date" id="demo" value="<?php echo e($time1); ?>  <?php echo e($time2); ?>" data-time1="<?php echo e($time1); ?>" data-time2="<?php echo e($time2); ?>"/>
                            <script type="text/javascript">
                                var time_range = function (beginTime, endTime) {
                                var strb = beginTime.split (":");
                                if (strb.length != 2) {
                                      return false;
                                  }
                              
                                  var stre = endTime.split (":");
                                  if (stre.length != 2) {
                                      return false;
                                 }
                             
                                 var b = new Date ();
                                 var e = new Date ();
                                 var n = new Date ();
                             
                                 b.setHours (strb[0]);
                                 b.setMinutes (strb[1]);
                                 e.setHours (stre[0]);
                                 e.setMinutes (stre[1]);
                             
                                 if (n.getTime () - b.getTime () > 0 && n.getTime () - e.getTime () < 0) {
                                     return true;
                                 } else {
                                     // alert ("当前时间是：" + n.getHours () + ":" + n.getMinutes () + "，不在该时间范围内！");
                                     return false;
                                 }
                             }
                                console.log(time_range ("15:00", "24:00"));
                                if(time_range ("15:00", "24:00")){
                                    $('input[name="date"]').daterangepicker();
                                    var div = document.getElementById("demo");
                                    var start = div.dataset.time1;//获取data-appid的值
                                    var end = div.dataset.time2;//获取data-myname的值
                                    // alert(time1,time2);



                                    function cb(start, end) {
                                        $('#demo').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
                                    }

                                    $('#demo').daterangepicker({
                                        "maxSpan": {
                                            "days": 7
                                        },
                                        ranges: {
                                            'Today': [moment(), moment()],
                                            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                                            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                                            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                                            'This Month': [moment().startOf('month'), moment().endOf('month')],
                                            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                                        },
                                        "alwaysShowCalendars": true,
                                        "startDate": start,
                                        "endDate": end,
                                    }, function(start, end, label) {
                                      console.log('New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')');
                                    });
                                }else{
                                    $('input[name="date"]').daterangepicker();
                                    var div = document.getElementById("demo");
                                    var start = div.dataset.time1;//获取data-appid的值
                                    var end = div.dataset.time2;//获取data-myname的值
                                    $('#demo').daterangepicker({
                                        "maxSpan": {
                                            "days": 7
                                        },
                                        ranges: {
                                            'Today': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                                            'Yesterday': [moment().subtract(2, 'days'), moment().subtract(2, 'days')],
                                            'Last 7 Days': [moment().subtract(7, 'days'),moment().subtract(1, 'days')],
                                            'Last 30 Days': [moment().subtract(30, 'days'), moment().subtract(1, 'days')],
                                            'This Month': [moment().startOf('month'), moment().subtract(1, 'days')],
                                            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                                        },
                                        "alwaysShowCalendars": true,
                                        "startDate": start,
                                        "endDate": end,
                                        
                                    });
                                }
                                    </script>
                                            <select class="form-control form-control-lg" name="appid" style="width: 100px;height: calc(2.875rem + 2px);margin-right: 20px" >
                                                <option value="App Search">App Search</option>

                                                <?php $__currentLoopData = $gamelist; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k=>$v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option  <?php if($appid==$v->id): ?> selected <?php endif; ?> value="<?php echo e($v->id); ?>"><?php echo e($v->name); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                            <!-- <select class="form-control form-control-lg" name="select" style="width: 50px;height: calc(2.875rem + 2px);margin-right: 20px">
                                                <option value="">Sort</option>
                                                <option  <?php if($select=='fb_ad_network_cpm'): ?> selected <?php endif; ?> value="fb_ad_network_cpm">FB ECPM</option>
                                                <option  <?php if($select=='am_cpm'): ?> selected <?php endif; ?> value="am_cpm">ADMOB ECPM</option>
                                                <option  <?php if($select=='profit'): ?> selected <?php endif; ?> value="profit">Profit</option>
                                                <option  <?php if($select=='fb_ad_network_revenue'): ?> selected <?php endif; ?> value="fb_ad_network_revenue">FAN</option>
                                                <option  <?php if($select=='is_revenue'): ?> selected <?php endif; ?> value="is_revenue">IronSoure Revenue</option>
                                                <option  <?php if($select=='mp_revenue'): ?> selected <?php endif; ?> value="mp_revenue">Mopub Revenue</option>
                                                <option  <?php if($select=='al_revenue'): ?> selected <?php endif; ?> value="al_revenue">Applovin Revenue</option>
                                                <option  <?php if($select=='am_revenue'): ?> selected <?php endif; ?> value="am_revenue">Admob Revenue</option>
                                                <option  <?php if($select=='cost'): ?> selected <?php endif; ?> value="cost">Spend</option>
                                                <option  <?php if($select=='install_count'): ?> selected <?php endif; ?> value="install_count">Install</option>
                                            </select>
                                            <select class="form-control form-control-lg" name="countryid" style="width: 100px;height: calc(2.875rem + 2px);margin-right: 20px">
                                                    <option value="">Country Search</option>
                                                    <option value="nanya">南亚次大陆</option>
                                                    <option value="beiou">北欧</option>
                                                    <option value="jialebi">加勒比</option>
                                                    <option value="lamei">拉美</option>
                                                    <option value="laomei">老美</option>
                                                    <option value="xiou">西欧</option>
                                                    <option value="nanou">南欧</option>
                                                    <option value="dongou">东欧</option>


                                                <?php $__currentLoopData = $countrylist; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k=>$v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option  <?php if($countryid==$v->code_2): ?> selected <?php endif; ?> value="<?php echo e($v->code_2); ?>"><?php echo e($v->code_2); ?>(<?php echo e($v->english); ?>)</option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select> -->
                                            <span class="input-group-append" >
                                                <button type="submit" class="btn btn-sm btn-gradient-primary"><i class="mdi mdi-account-search btn-icon-prepend"></i>
                                                    搜索
                                                </button>
                                            </span>   

                                        </div>
                                        
                                    </div>

                                </form>
                            </div>
                            
                            <!-- <table class="table table-bordered" > -->
                                <table id="form-table" cellpadding="0" border="0" cellspacing="0">
                                    <thead>
                                    <tr class="head">
                                        <th>Date</th>
                                        <th data-type="string">Country</th>
                                        <th data-type="string">Code</th>
                                        <th data-type="num">Installs</th>
                                        <th data-type="num">CPI</th>
                                        <th data-type="num">Spent</th>
                                        <!-- <th data-type="num">FAN</th>
                                        <th data-type="num">Admob</th>
                                        <th data-type="num">IronSoure</th>
                                        <th data-type="num">Mopub</th>
                                        <th data-type="num">Upltv</th>
                                        <th data-type="num">Adx Revenue</th>
                                        <th data-type="num">Revenue</th>
                                        <th data-type="num">Profit</th>
                                        <th data-type="roi">ROI</th>
                                        <th data-type="roi">CTR</th> -->
                                    </tr>
                                    <tr class="total" id="totalRow"></tr>
                                    </thead>
                                </table>


                                <!-- <tbody class="tbody"> -->
                                <div class="scroll-box">
                                    <table id="form-table1" cellpadding="0" cellspacing="0">
                                    <tbody>
                                    <?php $__currentLoopData = $list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k=>$v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <?php if($countryid): ?> <td><?php echo date('Y-m-d',$v->date);?></td> <?php else: ?> <td><?php echo date('m-d',strtotime($time1)).'~'.date('m-d',strtotime($time2));?></td> <?php endif; ?>
                                            <td><?php echo e($v->english); ?></td>
                                            <td><?php echo e($v->country_code); ?></td>
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
                                            <!-- <td><?php echo e(isset($v->fb_ad_network_revenue) ?$v->fb_ad_network_revenue : '0.00'); ?><span style="color: green"><?php echo e(isset($v->fb_ad_network_cpm) ?$v->fb_ad_network_cpm : '0.00'); ?></span></td>
                                            
                                            <td><?php echo e(isset($v->am_revenue) ?$v->am_revenue : '0.00'); ?>|<span style="color: green"><?php echo e(isset($v->am_cpm) ? $v->am_cpm: '0.00'); ?></span></td>
                                            <td><?php echo e(isset($v->is_revenue) ?$v->is_revenue : '0.00'); ?></td>
                                            <td><?php echo e(isset($v->mp_revenue) ?$v->mp_revenue : '0.00'); ?></td>
                                            <td><?php echo e(isset($v->upltv) ?$v->upltv : '0.00'); ?></td>
                                            <td><?php echo e(isset($v->adx_revenue) ?$v->adx_revenue : '0.00'); ?></td>
                                            <td>
                                                <?php 
                                                $total = $v->fb_ad_network_revenue+$v->is_revenue+$v->mp_revenue+$v->upltv+$v->am_revenue+$v->adx_revenue; 
                                                echo $total; 
                                                ?>
                                            </td> 
                                            <td>
                                                <?php 
                                                    echo round($v->profit,2); 
                                                ?>
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
                                            <?php 
                                                $ctr = $v->ctr*100; 
                                            ?> 
                                            <?php if($ctr >= 15): ?> 
                                                <td class="red"><?php echo $ctr.'%';?></td>
                                            <?php else: ?>
                                                <td class="green"><?php echo $ctr.'%';?></td>
                                            <?php endif; ?>
                                             -->
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
                </div>
            </div>
        </div>

    </div>

    <script>
        

       

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

        function download(appid,date){
            var data = {appid:appid,date:date,gamename:gamename};//参数
            myRequest("/admin/report/download","post",data,function(res){
                layer.msg(res.msg)
                setTimeout(function(){
                    window.location.reload();
                },500)
            });
        }
        (function () {
        var tbody = document.querySelector('#form-table1').tBodies[0];
        var th = document.querySelector('#form-table').tHead.rows[0].cells;
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
    <!-- 数据总计js -->
<script type="text/javascript">
    $(document).ready(function(){ 
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
        totalRow8 += parseFloat($(this).text()); 
      }); 
    }); 
      var totalRow9=0 
      $('#form-table1 tr').each(function(){ 
        $(this).find('td:eq(9)').each(function(){ 
        totalRow9 += parseFloat($(this).text()); 
      }); 
    }); 
      var totalRow10=0 
      $('#form-table1 tr').each(function(){ 
        $(this).find('td:eq(10)').each(function(){ 
        totalRow10+= parseFloat($(this).text()); 
      }); 
    }); 
      var totalRow11=0 
      $('#form-table1 tr').each(function(){ 
        $(this).find('td:eq(11)').each(function(){ 
        totalRow11 += parseFloat($(this).text()); 
      }); 
    }); 

      var totalRow12=0 
      $('#form-table1 tr').each(function(){ 
        $(this).find('td:eq(12)').each(function(){ 
        totalRow12 += parseFloat($(this).text()); 
      }); 
    }); 
      var totalRow13=0 
      $('#form-table1 tr').each(function(){ 
        $(this).find('td:eq(13)').each(function(){ 
        totalRow13 += parseFloat($(this).text()); 
      }); 
    }); 

      var totalRow14=0 
      $('#form-table1 tr').each(function(){ 
        $(this).find('td:eq(14)').each(function(){ 
        totalRow13 += parseFloat($(this).text()); 
      }); 
    }); 

       var totalRow15=0 
      $('#form-table1 tr').each(function(){ 
        $(this).find('td:eq(15)').each(function(){ 
        totalRow15 += parseFloat($(this).text()); 
      }); 
    }); 
      
    $('#totalRow').append('<td>Total</td><td>--</td><td>--</td><td>'+totalRow3+'</td><td>'+(totalRow5/totalRow3).toFixed(2)+'</td><td>'+totalRow5.toFixed(2)+'</td>'); 
    }); 
</script>


<?php $__env->stopSection(); ?>
<?php echo $__env->make('base.base', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>